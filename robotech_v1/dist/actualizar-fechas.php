<?php
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id'], $input['fecha_inicio'], $input['fecha_fin'])) {
    echo json_encode(["success" => false, "error" => "Faltan datos"]);
    exit;
}
include 'conexion.php';

$id = intval($input['id']);
$inicio = $conn->real_escape_string($input['fecha_inicio']);
$fin = $conn->real_escape_string($input['fecha_fin']);

$stmt = $conn->prepare("UPDATE tareas SET fecha_inicio = ?, fecha_limite = ? WHERE id = ?");
$stmt->bind_param("ssi", $inicio, $fin, $id);
$ok = $stmt->execute();

echo json_encode(["success" => $ok]);
