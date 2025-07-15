<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['organizacion_id'])) {
    header("Location: auth-login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "erp-crm");
$id = intval($_GET['id']);
$organizacion_id = intval($_SESSION['organizacion_id']);

// (Opcional) Validar si la serie está en uso
$enUso = $conn->query("SELECT COUNT(*) as total FROM facturas WHERE serie_id = $id")->fetch_assoc()['total'];
if ($enUso > 0) {
    echo "❌ No puedes eliminar esta serie porque está asociada a facturas existentes.";
    exit();
}

// Verificar que la serie pertenezca a la organización
$validar = $conn->query("SELECT id FROM series WHERE id = $id AND organizacion_id = $organizacion_id");
if ($validar->num_rows === 0) {
    echo "❌ No autorizado o serie no encontrada.";
    exit();
}

$conn->query("DELETE FROM series WHERE id = $id");
header("Location: series.php");
exit();
?>
