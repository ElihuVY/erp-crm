<?php
include 'conexion.php';

// Check if session exists and user is logged in
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    echo json_encode(['error' => 'No session found']);
    exit;
}

$user_id = $_SESSION['usuario']['id'];

// Obtener organizaciones del usuario
$sql = "SELECT organizacion_id FROM usuario_organizacion WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orgs = [];
while ($row = $result->fetch_assoc()) {
    $orgs[] = $row['organizacion_id'];
}

if (empty($orgs)) {
    echo json_encode([]);
    exit;
}

// Convertir IDs en una lista para IN (...)
$org_placeholders = implode(',', array_fill(0, count($orgs), '?'));

// Consulta series y contar facturas por serie
$query = "
    SELECT 
        s.id,
        s.nombre, 
        s.prefijo,
        s.siguiente_numero,
        COUNT(f.id) AS total_facturas
    FROM series s
    LEFT JOIN facturas f ON f.serie_id = s.id
    WHERE s.organizacion_id IN ($org_placeholders)
    GROUP BY s.id, s.nombre, s.prefijo, s.siguiente_numero
";

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('i', count($orgs)), ...$orgs);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = [
        'id' => $row['id'],
        'serie' => $row['nombre'],
        'prefijo' => $row['prefijo'],
        'siguiente_numero' => $row['siguiente_numero'],
        'facturas' => (int)$row['total_facturas']
    ];
}

header('Content-Type: application/json');
echo json_encode($datos);
exit;
?>
