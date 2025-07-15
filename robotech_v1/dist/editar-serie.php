<?php
session_start();
$conn = new mysqli("localhost", "root", "", "erp-crm");
$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $prefijo = $_POST['prefijo'] ?? '';
    $numero_inicial = intval($_POST['numero_inicial']);
    $numeracion_manual = isset($_POST['numeracion_manual']) ? 1 : 0;
    $reiniciar_anual = isset($_POST['reiniciar_anual']) ? 1 : 0;
    $rectificativa = isset($_POST['rectificativa']) ? 1 : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;
    $iva = isset($_POST['iva']) ? floatval($_POST['iva']) : 21.00;
    $irpf = isset($_POST['irpf']) ? floatval($_POST['irpf']) : 0.00;
    $impuestos_extra = !empty($_POST['impuestos_extra']) ? $_POST['impuestos_extra'] : null;

    $stmt = $conn->prepare("UPDATE series 
        SET nombre=?, prefijo=?, numero_inicial=?, numeracion_manual=?, reiniciar_anual=?, rectificativa=?, visible=?, iva=?, irpf=?, impuestos_extra=? 
        WHERE id=?");

    $stmt->bind_param("ssiiiiiddsi", 
        $nombre, $prefijo, $numero_inicial, 
        $numeracion_manual, $reiniciar_anual, $rectificativa, $visible, 
        $iva, $irpf, $impuestos_extra, $id
    );

    $stmt->execute();
    header("Location: series.php");
    exit();
}

$serie = $conn->query("SELECT * FROM series WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Serie</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">✏️ Editar Serie de Facturación</h2>

        <form method="POST" class="space-y-4">
            <label class="block font-medium">Nombre:
                <input type="text" name="nombre" class="border p-2 w-full mt-1" value="<?= htmlspecialchars($serie['nombre']) ?>" required>
            </label>

            <label class="block font-medium">Prefijo:
                <input type="text" name="prefijo" class="border p-2 w-full mt-1" value="<?= htmlspecialchars($serie['prefijo'] ?? '') ?>" maxlength="10">
            </label>

            <label class="block font-medium">Número Inicial:
                <input type="number" name="numero_inicial" class="border p-2 w-full mt-1" value="<?= intval($serie['numero_inicial']) ?>" required>
            </label>

            <hr class="my-4 border-gray-300">

            <h3 class="text-lg font-semibold">⚙️ Configuración Fiscal</h3>

            <label class="block font-medium">IVA (%):
                <input type="number" step="0.01" name="iva" class="border p-2 w-full mt-1" value="<?= floatval($serie['iva'] ?? 21) ?>">
            </label>

            <label class="block font-medium">IRPF (%):
                <input type="number" step="0.01" name="irpf" class="border p-2 w-full mt-1" value="<?= floatval($serie['irpf'] ?? 0) ?>">
            </label>

            <label class="block font-medium">Impuestos adicionales:
                <input type="text" name="impuestos_extra" class="border p-2 w-full mt-1" value="<?= htmlspecialchars($serie['impuestos_extra'] ?? '') ?>">
            </label>

            <hr class="my-4 border-gray-300">

            <div class="grid grid-cols-2 gap-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="numeracion_manual" <?= $serie['numeracion_manual'] ? 'checked' : '' ?>>
                    <span>Numeración Manual</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="reiniciar_anual" <?= $serie['reiniciar_anual'] ? 'checked' : '' ?>>
                    <span>Reiniciar Anualmente</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="rectificativa" <?= $serie['rectificativa'] ? 'checked' : '' ?>>
                    <span>Serie Rectificativa</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="visible" <?= $serie['visible'] ? 'checked' : '' ?>>
                    <span>Visible</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="series.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">⬅ Volver</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>
