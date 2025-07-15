<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['organizacion_id'])) {
    echo json_encode(["success" => false, "message" => "Sesión no válida."]);
    exit();
}

include 'conexion.php';

$id = intval($_POST['factura_id']);
$nuevo_estado = $_POST['estado'];

$estados_validos = ['borrador', 'emitida', 'pagada', 'vencida'];
if (!in_array($nuevo_estado, $estados_validos)) {
    echo json_encode(["success" => false, "message" => "❌ Estado no permitido."]);
    exit();
}

// Obtener estado actual
$res = $conn->query("SELECT estado FROM facturas WHERE id = $id");
if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "❌ Factura no encontrada."]);
    exit();
}

$factura = $res->fetch_assoc();
$estado_actual = $factura['estado'];

// 🔒 Lógica de bloqueo
$bloqueado = false;
$mensaje = "";

// Si ya está pagada, no puede cambiar más
if ($estado_actual === 'pagada') {
    $bloqueado = true;
    $mensaje = "❌ Una factura pagada no puede cambiar de estado.";
}

// De vencida solo puede pasar a pagada
if ($estado_actual === 'vencida' && $nuevo_estado !== 'pagada') {
    $bloqueado = true;
    $mensaje = "⚠️ Una factura vencida solo puede cambiar a 'pagada'.";
}

if ($bloqueado) {
    echo json_encode([
        "success" => false,
        "message" => $mensaje,
        "estado_actual" => $estado_actual
    ]);
    exit();
}

// ✅ Si pasa validación, actualizar
$stmt = $conn->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $nuevo_estado, $id);
$stmt->execute();

echo json_encode(["success" => true]);
