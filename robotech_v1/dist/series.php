<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: auth-login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "erp-crm");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$organizacion_id = $_SESSION['organizacion_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM series WHERE organizacion_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $organizacion_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Series de Facturación</title>
  <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-6xl mx-auto bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">📑 Series de Facturación</h1>
      <a href="crear-serie.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm">+ Crear Serie</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-300 text-sm">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="border px-4 py-2 text-left">Nombre</th>
            <th class="border px-4 py-2 text-left">Prefijo</th>
            <th class="border px-4 py-2 text-center">Nº Inicial</th>
            <th class="border px-4 py-2 text-center">Numeración</th>
            <th class="border px-4 py-2 text-center">Anual</th>
            <th class="border px-4 py-2 text-center">Rectificativa</th>
            <th class="border px-4 py-2 text-center">Visible</th>
            <th class="border px-4 py-2 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2"><?= htmlspecialchars($row['nombre']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($row['prefijo'] ?? '—') ?></td>
                <td class="border px-4 py-2 text-center"><?= $row['numero_inicial'] ?></td>
                <td class="border px-4 py-2 text-center"><?= $row['numeracion_manual'] ? 'Manual' : 'Automática' ?></td>
                <td class="border px-4 py-2 text-center"><?= $row['reiniciar_anual'] ? '✅' : '—' ?></td>
                <td class="border px-4 py-2 text-center"><?= $row['rectificativa'] ? '✅' : '—' ?></td>
                <td class="border px-4 py-2 text-center"><?= $row['visible'] ? '✅' : '—' ?></td>
                <td class="border px-4 py-2 text-center space-x-1">
                  <a href="editar-serie.php?id=<?= $row['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs inline-block">✏️ Editar</a>
                  <a href="eliminar-serie.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar esta serie?');" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs inline-block">🗑️ Eliminar</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center text-gray-500 py-6">No hay series de facturación creadas aún.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
