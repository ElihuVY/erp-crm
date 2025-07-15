<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: auth-login.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("ID del proyecto no proporcionado.");
}

$id = intval($_GET['id']);

// Obtener clientes y presupuestos para los selects
$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
$presupuestos = $conn->query("SELECT id FROM presupuestos ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// Obtener el proyecto
$stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Proyecto no encontrado.");
}
$proyecto = $resultado->fetch_assoc();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $presupuesto_id = $_POST['presupuesto_id'] !== '' ? $_POST['presupuesto_id'] : null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin = $_POST['fecha_fin'] ?: null;

    $stmt = $conn->prepare("UPDATE proyectos SET cliente_id = ?, presupuesto_id = ?, nombre = ?, descripcion = ?, estado = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?");
    $stmt->bind_param("iisssssi", $cliente_id, $presupuesto_id, $nombre, $descripcion, $estado, $fecha_inicio, $fecha_fin, $id);
    $stmt->execute();

    header("Location: proyectos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">✏️ Editar Proyecto</h2>

    <form method="POST" class="space-y-4">
        <label>Cliente:
            <select name="cliente_id" required class="border p-2 w-full">
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>" <?= $cliente['id'] == $proyecto['cliente_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cliente['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Presupuesto (opcional):
            <select name="presupuesto_id" class="border p-2 w-full">
                <option value="">Ninguno</option>
                <?php foreach ($presupuestos as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $proyecto['presupuesto_id'] ? 'selected' : '' ?>>
                        Presupuesto #<?= $p['id'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Nombre del Proyecto:
            <input type="text" name="nombre" value="<?= htmlspecialchars($proyecto['nombre']) ?>" required class="border p-2 w-full">
        </label>

        <label>Descripción:
            <textarea name="descripcion" class="border p-2 w-full"><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>
        </label>

        <label>Estado:
            <select name="estado" required class="border p-2 w-full">
                <?php
                $estados = ['planificado', 'en curso', 'completado', 'cancelado'];
                foreach ($estados as $estado) {
                    echo "<option value=\"$estado\" " . ($proyecto['estado'] === $estado ? 'selected' : '') . ">" . ucfirst($estado) . "</option>";
                }
                ?>
            </select>
        </label>

        <label>Fecha de inicio:
            <input type="date" name="fecha_inicio" value="<?= $proyecto['fecha_inicio'] ?>" class="border p-2 w-full">
        </label>

        <label>Fecha de fin:
            <input type="date" name="fecha_fin" value="<?= $proyecto['fecha_fin'] ?>" class="border p-2 w-full">
        </label>

        <div class="flex justify-between mt-6">
            <a href="proyectos.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">⬅ Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar cambios</button>
        </div>
    </form>
</div>
</body>
</html>
