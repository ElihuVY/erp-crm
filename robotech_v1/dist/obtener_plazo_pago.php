<?php
include 'conexion.php';

// Verificar que se recibió el ID del cliente
if (!isset($_GET['cliente_id']) || empty($_GET['cliente_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de cliente requerido']);
    exit;
}

$cliente_id = intval($_GET['cliente_id']);

try {
    // Consultar el plazo de pago del cliente
    $stmt = $conn->prepare("SELECT plazo_pago FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Devolver el plazo de pago (puede ser null)
        echo json_encode([
            'success' => true,
            'plazo_pago' => $row['plazo_pago'] ?? 'No especificado'
        ]);
    } else {
        // Cliente no encontrado
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Cliente no encontrado'
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Error en la consulta
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}

$conn->close();
?>