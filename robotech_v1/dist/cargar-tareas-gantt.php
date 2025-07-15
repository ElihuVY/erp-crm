<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$proyecto_id = $_GET['proyecto_id'] ?? null;
$estado = $_GET['estado'] ?? '';
$responsable = $_GET['responsable'] ?? '';

if (!$proyecto_id || !is_numeric($proyecto_id)) {
    echo json_encode([]);
    exit;
}

$where = "proyecto_id = $proyecto_id AND fecha_inicio IS NOT NULL AND fecha_limite IS NOT NULL";

if ($estado !== '') {
    $estado = $conn->real_escape_string($estado);
    $where .= " AND estado = '$estado'";
}

if ($responsable !== '') {
    $responsable = $conn->real_escape_string($responsable);
    $where .= " AND responsable = '$responsable'";
}

$sql = "SELECT 
    id,
    titulo AS nombre,
    fecha_inicio,
    fecha_limite AS fecha_fin,
    responsable,
    estado,
    CASE 
        WHEN estado = 'pendiente' THEN 0
        WHEN estado = 'en curso' THEN 50
        WHEN estado = 'finalizada' THEN 100
        ELSE 0
    END AS progreso,
    tarea_padre_id
FROM tareas
WHERE $where";

$tareas = [];
$hoy = date('Y-m-d');

$resultado = $conn->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $vaConRetraso = $row['estado'] !== 'finalizada' && $row['fecha_fin'] < $hoy;

    $icono = match($row['estado']) {
        'pendiente' => '📥 ',
        'en curso' => '🔄 ',
        'finalizada' => '✅ ',
        default => ''
    };

    $color = $vaConRetraso
        ? '#e53935' // rojo si va con retraso
        : match($row['estado']) {
            'pendiente' => '#9e9e9e',
            'en curso' => '#42a5f5',
            'finalizada' => '#66bb6a',
            default => '#ccc'
        };

    $tareas[] = [
        "id" => "T" . $row['id'],
        "name" => $icono . $row['nombre'],
        "start" => $row['fecha_inicio'],
        "end" => $row['fecha_fin'],
        "progress" => $row['progreso'],
        "estado" => $row['estado'],
        "responsable" => $row['responsable'],
        "color" => $color,
        "dependencies" => $row['tarea_padre_id'] ? "T" . $row['tarea_padre_id'] : ""
    ];
}

echo json_encode($tareas, JSON_UNESCAPED_UNICODE);
