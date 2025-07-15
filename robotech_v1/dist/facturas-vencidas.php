<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    die("<p class='text-red-600 font-semibold'>❌ Error: Usuario no válido.</p>");
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario'] ?? 'Desconocido';

$conn = new mysqli("localhost", "root", "", "erp-crm");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Obtener organización vinculada
$sql_org = "SELECT o.id, o.nombre 
            FROM organizaciones o 
            JOIN usuario_organizacion uo ON uo.organizacion_id = o.id 
            WHERE uo.usuario_id = ? LIMIT 1";
$stmt = $conn->prepare($sql_org);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$organizacion = $result->fetch_assoc();

if (!$organizacion) {
    die("<p class='text-red-600'>❌ No se encontró una organización asociada.</p>");
}

$organizacion_id = $organizacion['id'];
$organizacion_nombre = $organizacion['nombre'];
$hoy = date('Y-m-d');

// Facturas vencidas
$sql_vencidas = "SELECT f.id, f.fecha_emision, f.fecha_vencimiento, f.total, f.estado, c.nombre AS cliente
                 FROM facturas f
                 JOIN clientes c ON f.cliente_id = c.id
                 WHERE f.organizacion_id = ? 
                   AND f.estado != 'pagada'
                   AND f.fecha_vencimiento IS NOT NULL
                   AND f.fecha_vencimiento < ?
                 ORDER BY f.fecha_vencimiento ASC";
$stmt_v = $conn->prepare($sql_vencidas);
$stmt_v->bind_param("is", $organizacion_id, $hoy);
$stmt_v->execute();
$vencidas = $stmt_v->get_result();

// Facturas pendientes (no vencidas, solo estado 'emitida')
$sql_pendientes = "SELECT f.id, f.fecha_emision, f.fecha_vencimiento, f.total, f.estado, c.nombre AS cliente
                   FROM facturas f
                   JOIN clientes c ON f.cliente_id = c.id
                   WHERE f.organizacion_id = ? 
                     AND f.estado = 'emitida'
                     AND f.fecha_vencimiento IS NOT NULL
                     AND f.fecha_vencimiento >= ?
                   ORDER BY f.fecha_vencimiento ASC";
$stmt_p = $conn->prepare($sql_pendientes);
$stmt_p->bind_param("is", $organizacion_id, $hoy);
$stmt_p->execute();
$pendientes = $stmt_p->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📄 Facturas pendientes y vencidas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="max-w-6xl mx-auto pt-20 px-6">
    <h1 class="text-2xl font-bold mb-4">📄 Facturas pendientes y vencidas</h1>
    <p class="text-sm text-gray-600 mb-6">👤 Usuario: <strong><?= htmlspecialchars($usuario_nombre) ?></strong> — Organización: <strong><?= htmlspecialchars($organizacion_nombre) ?></strong></p>

    <!-- FACTURAS VENCIDAS -->
    <h2 class="text-lg font-semibold text-red-600 mb-2">⛔ Facturas vencidas y no pagadas</h2>
    <?php if ($vencidas->num_rows === 0): ?>
        <p class="text-green-600 font-medium mb-6">✅ No hay facturas vencidas.</p>
    <?php else: ?>
        <div class="overflow-x-auto bg-white shadow rounded-lg mb-10">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Cliente</th>
                        <th class="py-2 px-4 border">Fecha emisión</th>
                        <th class="py-2 px-4 border">Vencimiento</th>
                        <th class="py-2 px-4 border">Estado</th>
                        <th class="py-2 px-4 border">Total (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($f = $vencidas->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= $f['id'] ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($f['cliente']) ?></td>
                            <td class="py-2 px-4"><?= date("d/m/Y", strtotime($f['fecha_emision'])) ?></td>
                            <td class="py-2 px-4 text-red-600 font-semibold"><?= date("d/m/Y", strtotime($f['fecha_vencimiento'])) ?></td>
                            <td class="py-2 px-4 capitalize"><?= $f['estado'] ?></td>
                            <td class="py-2 px-4 font-semibold">€<?= number_format($f['total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- FACTURAS PENDIENTES (NO VENCIDAS) -->
    <h2 class="text-lg font-semibold text-yellow-600 mb-2">🕓 Facturas pendientes de cobro (no vencidas)</h2>
    <?php if ($pendientes->num_rows === 0): ?>
        <p class="text-green-600 font-medium">✅ No hay facturas pendientes.</p>
    <?php else: ?>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Cliente</th>
                        <th class="py-2 px-4 border">Fecha emisión</th>
                        <th class="py-2 px-4 border">Vencimiento</th>
                        <th class="py-2 px-4 border">Estado</th>
                        <th class="py-2 px-4 border">Total (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($f = $pendientes->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= $f['id'] ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($f['cliente']) ?></td>
                            <td class="py-2 px-4"><?= date("d/m/Y", strtotime($f['fecha_emision'])) ?></td>
                            <td class="py-2 px-4"><?= date("d/m/Y", strtotime($f['fecha_vencimiento'])) ?></td>
                            <td class="py-2 px-4 capitalize"><?= $f['estado'] ?></td>
                            <td class="py-2 px-4 font-semibold">€<?= number_format($f['total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
