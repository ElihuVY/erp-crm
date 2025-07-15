<?php
require __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include "conexion.php";

if (!isset($_GET['id'])) die("Factura no especificada.");

$id = intval($_GET['id']);

// --- Factura ---
$sql = "SELECT f.*, 
               c.nombre AS cliente_nombre, c.email, c.empresa, c.direccion AS cliente_direccion,
               o.nombre AS org_nombre, o.cif AS org_cif, o.direccion AS org_direccion, o.logo AS org_logo,
               s.prefijo
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        JOIN organizaciones o ON f.organizacion_id = o.id
        JOIN series s ON f.serie_id = s.id
        WHERE f.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die("Factura no encontrada.");
$factura = $res->fetch_assoc();

// --- Productos ---
$stmt_items = $conn->prepare("SELECT descripcion, cantidad, precio_unitario FROM factura_items WHERE factura_id = ?");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$productos = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

// --- Cálculos ---
$subtotal = 0;
foreach ($productos as $p) $subtotal += $p['cantidad'] * $p['precio_unitario'];
$iva_total = $subtotal * ($factura['iva'] / 100);
$irpf_total = $subtotal * ($factura['irpf'] / 100);
$total_final = $subtotal + $iva_total - $irpf_total;

// --- Logo Base64 ---
$logo_src = '';
if (!empty($factura['org_logo'])) {
    $ruta_logo = __DIR__ . '/' . $factura['org_logo'];
    if (file_exists($ruta_logo)) {
        $ext = pathinfo($ruta_logo, PATHINFO_EXTENSION);
        $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
        $logo_data = file_get_contents($ruta_logo);
        $logo_src = 'data:' . $mime . ';base64,' . base64_encode($logo_data);
    }
}


$qr_path = __DIR__ . "/qr_facturas/factura_$id.png";
$qr_src = '';
if (file_exists($qr_path)) {
    $qr_data = file_get_contents($qr_path);
    $qr_src = 'data:image/png;base64,' . base64_encode($qr_data);
}


// --- DomPDF ---
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// --- HTML PDF ---
$numero_formateado = $factura['prefijo'] . '-' . $factura['numero_factura'];
$html = "
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 20px 30px;
        color: #333;
    }
    .container {
        max-width: 100%;
    }
    .empresa-header {
        background: #f7f7f7;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 10px 15px;
        margin-bottom: 20px;
        font-size: 11px;
    }
    .empresa-header-table {
        width: 100%;
        border-collapse: collapse;
    }
    .empresa-header-table td {
        vertical-align: middle;
    }
    .logo img {
        height: 60px;
        object-fit: contain;
    }
    .info {
        text-align: right;
    }
    .factura-titulo {
        margin: 0 0 10px;
        font-size: 22px;
        font-weight: bold;
        color: #2c3e50;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
    }
    .info-linea {
        font-size: 11px;
        margin-bottom: 20px;
    }
    .datos {
        font-size: 11px;
        margin-bottom: 20px;
        background: #f9f9f9;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .datos h4 {
        margin: 0 0 5px;
        font-size: 12px;
        color: #2c3e50;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }
    th {
        background: #f5f5f5;
        padding: 8px;
        font-size: 11px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }
    td {
        padding: 6px 8px;
        font-size: 11px;
        border-bottom: 1px solid #eee;
    }
    .resumen {
        width: 40%;
        float: right;
        margin-top: 15px;
    }
    .resumen table {
        width: 100%;
        font-size: 11px;
    }
    .resumen td {
        padding: 5px 8px;
        text-align: right;
    }
    .resumen tr td:first-child {
        text-align: left;
    }
    .resumen tr.total td {
        font-weight: bold;
        background: #f0f0f0;
    }
    .pago {
        margin-top: 40px;
        font-size: 11px;
        padding: 10px;
        background: #f8f8f8;
        border: 1px solid #ddd;
        width: 60%;
    }
    .notas {
        margin-top: 30px;
        font-size: 11px;
        padding: 10px;
        background: #f4f4f4;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .footer {
        margin-top: 40px;
        text-align: center;
        font-size: 10px;
        color: #999;
    }
</style>

<div class='container'>
    <div class='empresa-header'>
        <table class='empresa-header-table'>
            <tr>
                <td class='logo'>";
if ($logo_src) {
    $html .= "<img src='{$logo_src}'>";
} else {
    $html .= "Sin logo";
}
$html .= "</td>
                <td class='info'>
                    <strong>{$factura['org_nombre']}</strong><br>
                    CIF: {$factura['org_cif']}<br>
                    {$factura['org_direccion']}
                </td>
            </tr>
        </table>
    </div>

    <div class='factura-titulo'>Factura {$numero_formateado}</div>
    <div class='info-linea'>
        <strong>Fecha emisión:</strong> " . date('d/m/Y', strtotime($factura['fecha_emision'])) . " &nbsp; | &nbsp;
        <strong>Fecha vencimiento:</strong> " . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . "
    </div>

    <div class='datos'>
        <h4>Datos del Cliente</h4>
        {$factura['cliente_nombre']}<br>
        {$factura['email']}<br>
        {$factura['cliente_direccion']}
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>";

foreach ($productos as $p) {
    $linea = $p['cantidad'] * $p['precio_unitario'];
    $html .= "<tr>
        <td>{$p['descripcion']}</td>
        <td>{$p['cantidad']}</td>
        <td>€" . number_format($p['precio_unitario'], 2, ',', '.') . "</td>
        <td>€" . number_format($linea, 2, ',', '.') . "</td>
    </tr>";
}

$html .= "</tbody>
    </table>

    <div class='resumen'>
        <table>
            <tr><td>Subtotal</td><td>€" . number_format($subtotal, 2, ',', '.') . "</td></tr>
            <tr><td>IVA ({$factura['iva']}%)</td><td>€" . number_format($iva_total, 2, ',', '.') . "</td></tr>";

if ($factura['irpf'] > 0) {
    $html .= "<tr><td>IRPF ({$factura['irpf']}%)</td><td>-€" . number_format($irpf_total, 2, ',', '.') . "</td></tr>";
}

$html .= "<tr class='total'><td>Total</td><td>€" . number_format($total_final, 2, ',', '.') . "</td></tr>
        </table>
    </div>

    <div style='clear:both'></div>

    <div style='display: flex; justify-content: space-between; align-items: center; margin-top: 40px;'>
        <div class='pago' style='width: 60%; margin-top: 0;'>
            <strong>Información de pago:</strong><br>
            Transferencia bancaria<br>
            Banco: Bancolita<br>
            Titular: {$factura['org_nombre']}<br>
            Nº Cuenta: ES00 0000 0000 0000 0000 0000
        </div>
        
        <!-- Código QR a la derecha de la información de pago -->
        ";
if ($qr_src) {
    $html .= "
        <div style='text-align: center; align-self: flex-start; padding-top: 10px;'>
            <img src='$qr_src' alt='Código QR' style='width:150px; height:auto;'>
        </div>
    </div>";
}


if (!empty($factura['notas'])) {
    $html .= "
    <div class='notas'>
        <strong>Notas:</strong><br>
        " . nl2br(htmlspecialchars($factura['notas'])) . "
    </div>";
}




$html .= "
    <div class='footer'>
        www.unsitiogeniales.es — Gracias por confiar en nosotros.
    </div>
</div>
";

// --- Renderizar PDF ---
ob_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Factura_{$numero_formateado}.pdf", ["Attachment" => true]);
