<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}

$hoy = date('Y-m-d'); // Simula si lo deseas: $hoy = '2024-06-01';

// Buscar todas las facturas madre recurrentes
$sql = "SELECT * FROM facturas 
        WHERE tipo = 'recurrente' 
        AND recurrente_hasta >= '$hoy'";
$result = $conn->query($sql);
$generadas = 0;

while ($factura = $result->fetch_assoc()) {
    $inicio = new DateTime($factura['fecha_emision']);
    $hasta = new DateTime($factura['recurrente_hasta']);
    $periodo = $factura['periodo_recurrente'];
    $cliente_id = $factura['cliente_id'];
    $organizacion_id = $factura['organizacion_id'];
    $total = $factura['total'];

    $interval = match($periodo) {
        'mensual' => new DateInterval('P1M'),
        'trimestral' => new DateInterval('P3M'),
        'anual' => new DateInterval('P1Y'),
        default => null
    };

    if (!$interval) continue;

    // Ver fechas ya generadas
    $fechas_existentes = [];
    $check = $conn->prepare("SELECT fecha_emision FROM facturas 
                             WHERE tipo = 'recurrente' 
                             AND cliente_id = ? 
                             AND total = ? 
                             AND periodo_recurrente = ?");
    $check->bind_param("ids", $cliente_id, $total, $periodo);
    $check->execute();
    $check_result = $check->get_result();
    while ($row = $check_result->fetch_assoc()) {
        $fechas_existentes[] = $row['fecha_emision'];
    }

    // Generar facturas faltantes desde el inicio hasta hoy
    $fecha = clone $inicio;
    while ($fecha <= $hasta && $fecha <= new DateTime($hoy)) {
        $f_emision = $fecha->format('Y-m-d');

        if (!in_array($f_emision, $fechas_existentes)) {
            $f_vencimiento = (clone $fecha)->modify('+15 days')->format('Y-m-d');

            $stmt = $conn->prepare("INSERT INTO facturas 
                (cliente_id, organizacion_id, tipo, estado, fecha_emision, fecha_vencimiento, total, periodo_recurrente, recurrente_hasta) 
                VALUES (?, ?, 'recurrente', 'emitida', ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iissdss",
                $cliente_id,
                $organizacion_id,
                $f_emision,
                $f_vencimiento,
                $total,
                $periodo,
                $factura['recurrente_hasta']
            );
            $stmt->execute();
            $generadas++;
        }

        $fecha->add($interval);
    }
}

echo "✅ Facturas generadas automáticamente: $generadas";
?>
