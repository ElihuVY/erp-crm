<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: auth-login.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("❌ ID del proyecto no especificado.");
}

$proyecto_id = intval($_GET['id']);

// Verificar si el proyecto existe
$check = $conn->prepare("SELECT id FROM proyectos WHERE id = ?");
$check->bind_param("i", $proyecto_id);
$check->execute();
$resCheck = $check->get_result();

if ($resCheck->num_rows === 0) {
    die("❌ El proyecto no existe.");
}

// Verificar si tiene tareas NO finalizadas
$tareas = $conn->prepare("SELECT id FROM tareas WHERE proyecto_id = ? AND estado != 'finalizada'");
$tareas->bind_param("i", $proyecto_id);
$tareas->execute();
$resTareas = $tareas->get_result();

if ($resTareas->num_rows > 0) {
    die("❌ No se puede eliminar el proyecto porque tiene tareas activas (pendiente o en curso).");
}

// Verificar si el proyecto está vinculado a una factura (si aplica lógica adicional)
// TODO: Añade aquí más comprobaciones si lo deseas

// Eliminar el proyecto
$delete = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
$delete->bind_param("i", $proyecto_id);
if ($delete->execute()) {
    header("Location: proyectos.php");
    exit();
} else {
    echo "❌ Error al eliminar el proyecto.";
}
?>
