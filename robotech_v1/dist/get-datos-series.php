<?php
if (!isset($_GET['serie_id'])) {
    echo json_encode(['error' => 'Serie no especificada']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "erp-crm");
$id = intval($_GET['serie_id']);
$sql = "SELECT iva, irpf, impuestos_extra FROM series WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode($res->fetch_assoc());
} else {
    echo json_encode(['error' => 'Serie no encontrada']);
}
