<?php
include 'conexion.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $conn->prepare("SELECT * FROM facturas WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $factura = $stmt->get_result()->fetch_assoc();
  echo json_encode($factura);
} else {
  echo json_encode(['error' => 'ID inválido']);
}
?>