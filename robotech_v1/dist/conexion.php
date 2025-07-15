<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe el archivo de configuración, redirige al asistente
if (!file_exists("config-db.json")) {
    header("Location: asistente.php");
    exit();
}

$config = json_decode(file_get_contents("config-db.json"), true);

$host = $config["host"];
$user = $config["user"];
$pass = $config["pass"];
$db   = $config["db"];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>

