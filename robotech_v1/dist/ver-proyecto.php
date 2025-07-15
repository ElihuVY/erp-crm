<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: auth-login.html");
    exit();
}

if (!isset($_GET['id'])) {
    die("❌ Proyecto no especificado.");
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = intval($_GET['id']);

// Obtener datos del proyecto con cliente y presupuesto
$stmt = $conn->prepare("SELECT p.*, c.nombre AS cliente, pr.id AS presupuesto_id, pr.importe AS presupuesto_importe FROM proyectos p JOIN clientes c ON p.cliente_id = c.id LEFT JOIN presupuestos pr ON p.presupuesto_id = pr.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("❌ Proyecto no encontrado.");
}

$proyecto = $resultado->fetch_assoc();

// Obtener tareas relacionadas
$tareas = [];
$tareas_q = $conn->prepare("SELECT * FROM tareas WHERE proyecto_id = ?");
$tareas_q->bind_param("i", $id);
$tareas_q->execute();
$tareas = $tareas_q->get_result()->fetch_all(MYSQLI_ASSOC);

// Calcular progreso
$total_tareas = count($tareas);
$completadas = count(array_filter($tareas, fn($t) => $t['estado'] === 'finalizada'));
$porcentaje = $total_tareas > 0 ? round(($completadas / $total_tareas) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Proyecto</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow space-y-4">
    <h1 class="text-2xl font-bold mb-4">📁 Detalle del Proyecto</h1>

    <p><strong>Cliente:</strong> <?= htmlspecialchars($proyecto['cliente']) ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($proyecto['nombre']) ?></p>
    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
    <p><strong>Estado:</strong> <?= ucfirst($proyecto['estado']) ?></p>
    <p><strong>Fecha de inicio:</strong> <?= $proyecto['fecha_inicio'] ?? '—' ?></p>
    <p><strong>Fecha de fin:</strong> <?= $proyecto['fecha_fin'] ?? '—' ?></p>

    <?php if ($proyecto['presupuesto_id']): ?>
        <p><strong>Presupuesto asociado:</strong> #<?= $proyecto['presupuesto_id'] ?> - <?= number_format($proyecto['presupuesto_importe'], 2) ?> €</p>
    <?php else: ?>
        <p><strong>Presupuesto asociado:</strong> Ninguno</p>
    <?php endif; ?>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-2">📊 Progreso del proyecto</h2>
    <div class="w-full bg-gray-200 rounded-full h-4">
        <div class="bg-green-500 h-4 rounded-full text-xs text-center text-white" style="width: <?= $porcentaje ?>%">
            <?= $porcentaje ?>%
        </div>
    </div>

    <h2 class="text-xl font-semibold mt-6">📝 Tareas asociadas</h2>

    <?php if (count($tareas) === 0): ?>
        <p class="text-gray-600">No hay tareas registradas para este proyecto.</p>
    <?php else: ?>
        <table class="w-full table-auto border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Título</th>
                    <th class="p-2 border">Estado</th>
                    <th class="p-2 border">Responsable</th>
                    <th class="p-2 border">Prioridad</th>
                    <th class="p-2 border">Inicio</th>
                    <th class="p-2 border">Límite</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $t): ?>
                    <tr>
                        <td class="p-2 border"><?= htmlspecialchars($t['titulo']) ?></td>
                        <td class="p-2 border"><?= ucfirst($t['estado']) ?></td>
                        <td class="p-2 border"><?= htmlspecialchars($t['responsable'] ?: '—') ?></td>
                        <td class="p-2 border"><?= $t['prioridad'] ? ucfirst($t['prioridad']) : '—' ?></td>
                        <td class="p-2 border"><?= $t['fecha_inicio'] ?? '—' ?></td>
                        <td class="p-2 border"><?= $t['fecha_limite'] ?? '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="mt-6 flex justify-between items-center">
        <a href="proyectos.php" class="text-blue-600 hover:underline">⬅ Volver a la lista de proyectos</a>
    </div>
</div>
</body>
</html>
