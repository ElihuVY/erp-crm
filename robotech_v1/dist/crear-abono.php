<?php
include 'conexion.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$factura_id = intval($input['factura_origen_id'] ?? 0); // ← corregido
$items = $input['items'] ?? [];

if ($factura_id <= 0 || empty($items)) {
    echo json_encode(['error' => 'Datos incompletos para crear el abono']);
    exit;
}


// Obtener datos de la factura original
$stmt = $conn->prepare("SELECT * FROM facturas WHERE id = ?");
$stmt->bind_param("i", $factura_id);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();

if (!$factura) {
    echo json_encode(['error' => 'Factura original no encontrada']);
    exit;
}

// Calcular total del abono (negativo)
$total_abono = 0;
foreach ($items as $item) {
    // Acepta tanto 'precio' como 'precio_unitario'
    $precio = isset($item['precio']) ? $item['precio'] : (isset($item['precio_unitario']) ? $item['precio_unitario'] : 0);
    $cantidad = floatval($item['cantidad']);
    $total_abono += $cantidad * floatval($precio);
}
$total_abono = -abs($total_abono); // Siempre negativo

// Extraer los valores de la factura original
$cliente_id = $factura['cliente_id'];
$organizacion_id = $factura['organizacion_id'];
$serie_id = $factura['serie_id'];
$numero_factura = $factura['numero_factura'] . '-A'; // Añadir sufijo para indicar que es un abono
$tipo = 'abono';
$estado = 'emitida';
$fecha_emision = date('Y-m-d');
$fecha_vencimiento = date('Y-m-d', strtotime('+30 days'));
$periodo = $factura['periodo_recurrente'];
$hasta = $factura['recurrente_hasta'];
$iva = $factura['iva'];
$irpf = $factura['irpf'];
$impuestos_extra = $factura['impuestos_extra'];
$notas = "Abono de la factura " . $factura['numero_factura'];

// Crear la nueva factura tipo abono
$stmt = $conn->prepare("INSERT INTO facturas 
        (cliente_id, organizacion_id, serie_id, numero_factura, tipo, estado, fecha_emision, fecha_vencimiento, total, periodo_recurrente, recurrente_hasta, iva, irpf, impuestos_extra, notas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisssssdssddss", $cliente_id, $organizacion_id, $serie_id, $numero_factura, $tipo, $estado, $fecha_emision, $fecha_vencimiento, $total_abono, $periodo, $hasta, $iva, $irpf, $impuestos_extra, $notas);
$stmt->execute();
$nueva_factura_id = $stmt->insert_id;

// Insertar los items seleccionados en la nueva factura de abono
foreach ($items as $item) {
    // Usar descripción del producto o un valor predeterminado si no existe 'nombre'
    $descripcion = isset($item['nombre']) ? $conn->real_escape_string($item['nombre']) : 
                  (isset($item['descripcion']) ? $conn->real_escape_string($item['descripcion']) : 'Producto sin descripción');
    
    $cantidad = floatval($item['cantidad'] ?? 1);
    
    // Usar el precio que ya calculamos anteriormente
    $precio_unitario = isset($item['precio']) ? floatval($item['precio']) : 
                      (isset($item['precio_unitario']) ? floatval($item['precio_unitario']) : 0);
    
    $producto_id = intval($item['producto_id'] ?? 0);

    $stmt_item = $conn->prepare("INSERT INTO factura_items (factura_id, producto_id, descripcion, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");
    $stmt_item->bind_param("iisdd", $nueva_factura_id, $producto_id, $descripcion, $cantidad, $precio_unitario);
    $stmt_item->execute();
}

echo json_encode(['success' => true, 'nueva_factura_id' => $nueva_factura_id]);
?>