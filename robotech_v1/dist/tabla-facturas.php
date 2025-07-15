<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida");
}

$buscar = $conn->real_escape_string($_GET['buscar'] ?? '');
$estado = $_GET['estado'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$condiciones = [];
if ($buscar !== '') $condiciones[] = "c.nombre LIKE '%$buscar%'";
if ($estado !== '') $condiciones[] = "f.estado = '$estado'";
if ($tipo !== '') $condiciones[] = "f.tipo = '$tipo'";
$where_sql = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$total_query = "SELECT COUNT(*) as total FROM facturas f 
                JOIN clientes c ON f.cliente_id = c.id $where_sql";
$total_result = $conn->query($total_query);
$total_registros = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

$query = "SELECT f.*, c.nombre AS cliente FROM facturas f 
          JOIN clientes c ON f.cliente_id = c.id 
          $where_sql
          ORDER BY f.id DESC
          LIMIT $por_pagina OFFSET $offset";
$result = $conn->query($query);

$html = '';
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
        <td class='p-2 border'>{$row['id']}</td>
        <td class='p-2 border'>" . htmlspecialchars($row['cliente']) . "</td>
        <td class='p-2 border'>{$row['tipo']}</td>
        <td class='p-2 border'>
            <form method='POST' action='cambiar-estado-factura.php'>
                <input type='hidden' name='factura_id' value='{$row['id']}'>
                <select name='estado' onchange='this.form.submit()' class='border p-1 rounded text-sm'>
                    <option value='borrador'" . ($row['estado'] === 'borrador' ? ' selected' : '') . ">Borrador</option>
                    <option value='emitida'" . ($row['estado'] === 'emitida' ? ' selected' : '') . ">Emitida</option>
                    <option value='pagada'" . ($row['estado'] === 'pagada' ? ' selected' : '') . ">Pagada</option>
                    <option value='vencida'" . ($row['estado'] === 'vencida' ? ' selected' : '') . ">Vencida</option>
                </select>
            </form>
        </td>
        <td class='p-2 border'>{$row['fecha_emision']}</td>
        <td class='p-2 border'>{$row['fecha_vencimiento']}</td>
        <td class='p-2 border'>€" . number_format($row['total'], 2) . "</td>
        <td class='p-2 border'>" . ($row['tipo'] === 'recurrente' ? ucfirst($row['periodo_recurrente']) : '—') . "</td>
        <td class='p-2 border'>
            <a href='ver-factura.php?id={$row['id']}' class='text-blue-600 hover:underline'>Ver</a>" .
            ($row['estado'] === 'borrador' ? " | <a href='editar-factura.php?id={$row['id']}' class='text-orange-600 hover:underline'>Editar</a>" : '') .
            " | <a href='descargar-factura.php?id={$row['id']}' class='text-green-600 hover:underline'>PDF</a>
            | <a href='enviar-factura.php?id={$row['id']}' class='text-indigo-600 hover:underline'>Enviar</a>
        </td>
    </tr>";
}

$paginacion = '';
for ($i = 1; $i <= $total_paginas; $i++) {
    $activo = $i === $pagina ? 'bg-blue-500 text-white' : 'bg-white text-blue-600';
    $paginacion .= "<a href='#' data-pagina='$i' class='pagina-link px-3 py-1 border rounded $activo'>$i</a>";
}

echo json_encode([
    'html' => $html,
    'paginacion' => $paginacion
]);
