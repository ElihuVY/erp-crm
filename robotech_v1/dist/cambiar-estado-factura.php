<?php
include 'conexion.php';

header('Content-Type: application/json'); // Muy importante

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nuevo_estado = $_POST['estado'];

    $estados_validos = ['borrador', 'emitida', 'pagada', 'vencida'];
    if (!in_array($nuevo_estado, $estados_validos)) {
        echo json_encode([
            'error' => "❌ Estado inválido.",
        ]);
        exit;
    }

    // Obtener estado actual
    $stmt = $conn->prepare("SELECT estado FROM facturas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($estado_actual);
    if (!$stmt->fetch()) {
        echo json_encode([
            'error' => "❌ Factura no encontrada.",
        ]);
        exit;
    }
    $stmt->close();

    // $transiciones_validas = [
    //     'borrador' => ['emitida'],
    //     'emitida' => ['pagada', 'vencida'],
    //     'vencida' => ['pagada'],
    //     'pagada' => []
    // ];

    // if (!in_array($nuevo_estado, $transiciones_validas[$estado_actual])) {
    //     echo json_encode([
    //         'error' => "❌ No puedes cambiar una factura de $estado_actual a $nuevo_estado",
    //         'estado_actual' => $estado_actual,
    //         'showAlert' => true, // Flag to trigger alert
    //         'message' => "No se puede cambiar la factura de $estado_actual a $nuevo_estado", // Alert message
    //         'timeout' => 5000 // 5 seconds timeout
    //     ]);
    //     exit;
    // }

    // Ejecutar cambio válido
    $stmt = $conn->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'estado' => $nuevo_estado
    ]);
    exit;
}
?>