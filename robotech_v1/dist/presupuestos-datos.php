<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "erp-crm");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Error de conexión"]);
    exit();
}

$organizacion_id = intval($_GET['organizacion_id'] ?? 0);
$anio = intval($_GET['anio'] ?? date('Y'));
$periodo = $_GET['periodo'] ?? 'mensual';

if (!$organizacion_id || !$anio) {
    echo json_encode([]);
    exit();
}

$labels = [];
$datos = [];
$emitidos_por_periodo = [];
$aceptados_por_periodo = [];

switch ($periodo) {
    case 'mensual':
        $labels = [
            1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun",
            7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic"
        ];
        $sql = "
            SELECT MONTH(fecha_emision) AS periodo, estado, COUNT(*) AS cantidad
            FROM presupuestos
            WHERE organizacion_id = ? AND YEAR(fecha_emision) = ?
            GROUP BY periodo, estado
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $organizacion_id, $anio);
        break;

    case 'trimestral':
        $labels = [
            1 => "1º Trimestre", 2 => "2º Trimestre", 3 => "3º Trimestre", 4 => "4º Trimestre"
        ];
        $sql = "
            SELECT QUARTER(fecha_emision) AS periodo, estado, COUNT(*) AS cantidad
            FROM presupuestos
            WHERE organizacion_id = ? AND YEAR(fecha_emision) = ?
            GROUP BY periodo, estado
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $organizacion_id, $anio);
        break;

    case 'anual':
        $sql = "
            SELECT YEAR(fecha_emision) AS periodo, estado, COUNT(*) AS cantidad
            FROM presupuestos
            WHERE organizacion_id = ?
            GROUP BY periodo, estado
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $organizacion_id);
        break;

    default:
        echo json_encode([]);
        exit();
}

$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $p = intval($row['periodo']);
    $estado = strtolower($row['estado']);
    $cantidad = intval($row['cantidad']);

    if (!isset($emitidos_por_periodo[$p])) {
        $emitidos_por_periodo[$p] = 0;
        $aceptados_por_periodo[$p] = 0;
    }

    if ($estado === 'emitido') {
        $emitidos_por_periodo[$p] += $cantidad;
    } elseif ($estado === 'aceptado') {
        $aceptados_por_periodo[$p] += $cantidad;
    }
}

foreach ($labels as $periodo_key => $label) {
    $datos[] = [
        "label" => $label,
        "emitidos" => $emitidos_por_periodo[$periodo_key] ?? 0,
        "aceptados" => $aceptados_por_periodo[$periodo_key] ?? 0
    ];
}

echo json_encode($datos);
