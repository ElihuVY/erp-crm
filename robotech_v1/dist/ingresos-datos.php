<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Error de conexión"]);
    exit();
}

// Recoger parámetros
$organizacion_id = intval($_GET['organizacion_id'] ?? 0);
$periodo = $_GET['periodo'] ?? 'mensual';
$anio = intval($_GET['anio'] ?? date('Y'));

if (!$organizacion_id || !$anio) {
    echo json_encode([]);
    exit();
}

// Variables
$labels = [];
$facturasPorPeriodo = [];
$datos = [];

// SQL y agrupación por tipo de periodo
switch ($periodo) {
    case 'mensual':
        $labels = [
            1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun",
            7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic"
        ];
        $sql = "
            SELECT f.id, f.numero_serie, f.fecha_emision, f.total, s.prefijo, MONTH(f.fecha_emision) AS periodo
            FROM facturas f
            LEFT JOIN series s ON f.serie_id = s.id
            WHERE f.organizacion_id = ? AND YEAR(f.fecha_emision) = ?
            ORDER BY f.fecha_emision ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $organizacion_id, $anio);
        break;

    case 'trimestral':
        $labels = [
            1 => "1º Trimestre", 2 => "2º Trimestre", 3 => "3º Trimestre", 4 => "4º Trimestre"
        ];
        $sql = "
            SELECT f.id, f.numero_serie, f.fecha_emision, f.total, s.prefijo, QUARTER(f.fecha_emision) AS periodo
            FROM facturas f
            LEFT JOIN series s ON f.serie_id = s.id
            WHERE f.organizacion_id = ? AND YEAR(f.fecha_emision) = ?
            ORDER BY f.fecha_emision ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $organizacion_id, $anio);
        break;

    case 'anual':
        $sql = "
            SELECT f.id, f.numero_serie, f.fecha_emision, f.total, s.prefijo, YEAR(f.fecha_emision) AS periodo
            FROM facturas f
            LEFT JOIN series s ON f.serie_id = s.id
            WHERE f.organizacion_id = ? AND f.fecha_emision IS NOT NULL
            ORDER BY f.fecha_emision ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $organizacion_id);
        break;

    default:
        echo json_encode([]);
        exit();
}

// Ejecutar la consulta
$stmt->execute();
$res = $stmt->get_result();

// Agrupar facturas por periodo
while ($row = $res->fetch_assoc()) {
    $periodo_key = intval($row['periodo']);
    $label = $labels[$periodo_key] ?? "Periodo $periodo_key";

    $numero_factura = ($row['prefijo'] && $row['numero_serie'])
        ? $row['prefijo'] . '-' . $row['numero_serie']
        : 'Sin número';

    $factura = [
        "id"     => $row['id'],
        "numero" => $numero_factura,
        "fecha"  => date("d/m/Y", strtotime($row['fecha_emision'])),
        "total"  => number_format($row['total'], 2, '.', '')
    ];

    if (!isset($facturasPorPeriodo[$label])) {
        $facturasPorPeriodo[$label] = [
            "label"    => $label,
            "total"    => 0,
            "facturas" => []
        ];
    }

    $facturasPorPeriodo[$label]["facturas"][] = $factura;
    $facturasPorPeriodo[$label]["total"] += $row['total'];
}

// Formatear total y preparar JSON final
foreach ($facturasPorPeriodo as $periodo) {
    $periodo['total'] = number_format($periodo['total'], 2, '.', '');
    $datos[] = $periodo;
}

echo json_encode($datos);
