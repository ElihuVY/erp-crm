
<?php
//require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/dompdf/autoload.inc.php';

require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/Exception.php';
include 'conexion.php'; // conexión a tu base de datos

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


session_start();

$facturas_ids = $_POST['facturas'] ?? [];
if (empty($facturas_ids)) {
    die("IDs de facturas no proporcionados.");
}

$mail = new PHPMailer(true);

try {
    // Configura SMTP 
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'elihu.practicas@gmail.com';
    $mail->Password = 'xfyw cnum gcof hiwh';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

  //   $mail->SMTPOptions = array(
  //     'ssl' => array(
  //         'verify_peer' => false,
  //         'verify_peer_name' => false,
  //         'allow_self_signed' => true
  //     )
  // );

    $mail->setFrom('elihu.practicas@gmail.com', 'Tu Empresa');
    $mail->addAddress($_POST['destinatario']); // El correo al que enviar

    $mail->Subject = $_POST['asunto'] ?? 'Facturas adjuntas';
    $mail->Body = $_POST['mensaje'] ?? 'Adjuntamos las facturas.';

    // Procesar cada factura
    foreach ($facturas_ids as $factura_id) {
        // Obtener datos de factura
        $sql = "SELECT f.*, c.nombre AS cliente_nombre, c.email AS cliente_email, c.empresa, c.direccion,
                       o.nombre AS org_nombre, o.cif AS org_cif, o.direccion AS org_direccion
                FROM facturas f
                JOIN clientes c ON f.cliente_id = c.id
                JOIN organizaciones o ON f.organizacion_id = o.id
                WHERE f.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $factura_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            continue; // Si no existe esta factura, pasa a la siguiente
        }
        $factura = $result->fetch_assoc();

        // Obtener items de la factura
        $stmt_items = $conn->prepare("SELECT descripcion, cantidad, precio_unitario FROM factura_items WHERE factura_id = ?");
        $stmt_items->bind_param("i", $factura_id);
        $stmt_items->execute();
        $res_items = $stmt_items->get_result();

        $items_html = "";
        $total_neto = 0;
        while ($item = $res_items->fetch_assoc()) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total_neto += $subtotal;

            $items_html .= "
                <tr>
                    <td style='padding:8px; border:1px solid #ccc;'>{$item['descripcion']}</td>
                    <td style='padding:8px; text-align:center; border:1px solid #ccc;'>{$item['cantidad']}</td>
                    <td style='padding:8px; text-align:right; border:1px solid #ccc;'>€" . number_format($item['precio_unitario'], 2) . "</td>
                    <td style='padding:8px; text-align:right; border:1px solid #ccc;'>€" . number_format($subtotal, 2) . "</td>
                </tr>";
        }

        // Crear HTML para PDF (simplificado)
        $html = "
        <h1>Factura #{$factura['id']}</h1>
        <p>Cliente: {$factura['cliente_nombre']}</p>
        <p>Empresa: {$factura['org_nombre']}</p>
        <table style='border-collapse: collapse; width: 100%;'>
            <thead>
                <tr>
                    <th style='border:1px solid #ccc; padding:8px;'>Descripción</th>
                    <th style='border:1px solid #ccc; padding:8px;'>Cantidad</th>
                    <th style='border:1px solid #ccc; padding:8px;'>Precio Unitario</th>
                    <th style='border:1px solid #ccc; padding:8px;'>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                $items_html
            </tbody>
        </table>
        <p>Total Neto: €" . number_format($total_neto, 2) . "</p>
        ";

        // Generar PDF con Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Obtener PDF en variable
        $pdfOutput = $dompdf->output();

        // Adjuntar PDF al correo
        $mail->addStringAttachment($pdfOutput, "Factura_{$factura['id']}.pdf");
    }

    // Enviar correo
    $mail->send();
    $_SESSION['estado_envio'] = 'ok';
    

} catch (Exception $e) {
    $_SESSION['estado_envio'] = 'error';
}
header("Location: facturas.php");
exit();




?>

