<?php
header('Content-Type: application/json');

include 'conexion.php';
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no especificado']);
    exit;
}

$serie_id = intval($_GET['id']);


$sql = "SELECT iva, irpf, impuestos_extra, numeracion_manual FROM series WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $serie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Serie no encontrada']);
    exit;
}

$serie = $result->fetch_assoc();
echo json_encode([
    'success' => true,
    'iva' => floatval($serie['iva']),
    'irpf' => floatval($serie['irpf']),
    'impuestos_extra' => $serie['impuestos_extra'],
    'numeracion_manual' => $serie['numeracion_manual']
]);
