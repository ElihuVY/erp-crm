<?php
// facturas-data.php

include 'conexion.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

$anioActual = (int)date('Y');
$anioAnterior = $anioActual - 1;

function obtenerTotalesPorMes($conn, $anio) {
    $stmt = $conn->prepare("SELECT MONTH(fecha_emision) as mes, SUM(total) as total FROM facturas WHERE YEAR(fecha_emision) = ? GROUP BY mes");
    if (!$stmt) {
        return array_fill(0, 12, 0);
    }

    $stmt->bind_param("i", $anio);
    $stmt->execute();

    $result = $stmt->get_result();

    $totales = array_fill(0, 12, 0); // indices 0 a 11 (enero a diciembre)

    while ($row = $result->fetch_assoc()) {
        $mes = (int)$row['mes'];
        $totales[$mes - 1] = (float)$row['total']; // mes-1 para índice 0-based
    }

    $stmt->close();

    return $totales;
}

$data = [
    'lastYear' => obtenerTotalesPorMes($conn, $anioAnterior),
    'thisYear' => obtenerTotalesPorMes($conn, $anioActual)
];

header('Content-Type: application/json');
echo json_encode($data);
