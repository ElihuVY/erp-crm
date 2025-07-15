<?php
// Conexión a la base de datos
include 'conexion.php';// asegúrate de que este archivo establece $conn correctamente

// Verifica que se recibió el parámetro
if (!isset($_GET['cliente_id'])) {
    echo json_encode(['error' => 'ID de cliente no especificado']);
    exit;
}

$cliente_id = intval($_GET['cliente_id']);

// Consulta para obtener el plazo de pago del cliente
$stmt = $conn->prepare("SELECT plazo_pago FROM clientes WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Cliente no encontrado']);
} else {
    $row = $result->fetch_assoc();
    echo json_encode(['plazo_pago' => $row['plazo_pago']]);
}

$stmt->close();
$conn->close();
