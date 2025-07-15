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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organizacion_id = $_SESSION['organizacion_id'];
    $nombre = $_POST['nombre'];
    $prefijo = $_POST['prefijo'];
    $numero_inicial = intval($_POST['numero_inicial']);
    $numeracion_manual = isset($_POST['numeracion_manual']) ? 1 : 0;
    $reiniciar_anual = isset($_POST['reiniciar_anual']) ? 1 : 0;
    $rectificativa = isset($_POST['rectificativa']) ? 1 : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;
    $iva = isset($_POST['iva']) ? floatval($_POST['iva']) : 21.00;
    $irpf = isset($_POST['irpf']) ? floatval($_POST['irpf']) : 0.00;
    $impuestos_extra = !empty($_POST['impuestos_extra']) ? $_POST['impuestos_extra'] : null;

    $stmt = $conn->prepare("INSERT INTO series 
        (organizacion_id, nombre, prefijo, numero_inicial, numeracion_manual, reiniciar_anual, rectificativa, visible, iva, irpf, impuestos_extra, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("issiiiiidds", 
        $organizacion_id, $nombre, $prefijo, $numero_inicial, 
        $numeracion_manual, $reiniciar_anual, $rectificativa, $visible, 
        $iva, $irpf, $impuestos_extra
    );

    $stmt->execute();
    header("Location: series.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Serie</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">✨ Crear Nueva Serie de Facturación</h2>

        <form method="POST" class="space-y-4">
            <label class="block font-medium">Nombre:
                <input type="text" name="nombre" class="border p-2 w-full mt-1" required>
            </label>

            <label class="block font-medium">Prefijo:
                <input type="text" name="prefijo" class="border p-2 w-full mt-1" placeholder="Ej. AUT, FAC, INV" required>
            </label>

            <label class="block font-medium">Número Inicial:
                <input type="number" name="numero_inicial" value="1" class="border p-2 w-full mt-1" required>
            </label>

            <hr class="my-4 border-gray-300">

            <h3 class="text-lg font-semibold">⚙️ Configuración Fiscal</h3>

            <label class="block font-medium">IVA (%):
                <input type="number" name="iva" step="0.01" value="21.00" class="border p-2 w-full mt-1">
            </label>

            <label class="block font-medium">IRPF (%):
                <input type="number" name="irpf" step="0.01" value="0.00" class="border p-2 w-full mt-1">
            </label>

            <label class="block font-medium">Impuestos adicionales:
                <input type="text" name="impuestos_extra" class="border p-2 w-full mt-1" placeholder="Ej: Recargo de equivalencia">
            </label>

            <hr class="my-4 border-gray-300">

            <div class="grid grid-cols-2 gap-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="numeracion_manual">
                    <span>Numeración Manual</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="reiniciar_anual">
                    <span>Reiniciar Anualmente</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="rectificativa">
                    <span>Serie Rectificativa</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="visible" checked>
                    <span>Visible</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="series.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">⬅ Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">💾 Guardar Serie</button>
            </div>
        </form>
    </div>
</body>
</html>
