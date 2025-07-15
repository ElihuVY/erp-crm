<?php
include 'conexion.php';

// Get all organizations associated with current user
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT organizacion_id FROM usuario_organizacion WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$organizaciones_permitidas = [];
while ($row = $result->fetch_assoc()) {
    $organizaciones_permitidas[] = $row['organizacion_id'];
}

if (empty($organizaciones_permitidas)) {
    die("❌ No tienes organizaciones asociadas.");
}

if (!isset($_GET['id'])) {
    die("❌ ID de producto no especificado.");
}

$id = intval($_GET['id']);

// Get product checking against all allowed organizations
$organizaciones_str = implode(',', $organizaciones_permitidas);
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ? AND organizacion_id IN ($organizaciones_str)");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Producto no encontrado o no pertenece a ninguna de tus organizaciones.");
}
$producto = $result->fetch_assoc();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $precio = floatval($_POST['precio']);
    $iva = floatval($_POST['iva']);

    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, tipo = ?, precio = ?, iva = ? WHERE id = ? AND organizacion_id = ?");
    $stmt->bind_param("sssddii", $nombre, $descripcion, $tipo, $precio, $iva, $id, $organizacion_id);
    $stmt->execute();

    header("Location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- CSS -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">

    <style>
        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }

        .py-30 {
            padding-left: 15px;
        }
    </style>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <?php include 'menu.php'; ?>

    <div class="xl:w-full">
        <main class="pt-[90px] px-4 pb-16 lg:ms-[260px]">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <nav class="flex mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="inicio.php" class="text-gray-500 hover:text-gray-700 inline-flex items-center">
                                    <i class="icofont-home w-4 h-4 mr-2"></i>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="icofont-simple-right text-gray-400"></i>
                                    <a href="productos.php" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Productos</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <i class="icofont-simple-right text-gray-400"></i>
                                    <span class="ml-1 text-gray-400 md:ml-2">Editar</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">

                <!-- Main Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between px-6 py-2">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            ✏️ Editar Producto
                        </h1>
                        <div class="bg-green-100 text-green-800 text-lg font-semibold px-6 py-3 rounded-full shadow-sm">
                            ID: #<?= $producto['id'] ?>
                        </div>
                    </div>
                    <!-- Card Header -->
                    <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-700">

                        <div class="flex items-center">
                            <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                                <i class="icofont-edit text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Información del Producto</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Modifica los datos del producto</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-12">
                        <form method="POST" class="space-y-12">

                            <!-- Información General -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Nombre -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-tag mr-1"></i>
                                        Nombre del Producto *
                                    </label>
                                    <input type="text"
                                        id="nombre"
                                        name="nombre"
                                        value="<?= htmlspecialchars($producto['nombre']) ?>"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- Tipo -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-layers mr-1"></i>
                                        Tipo *
                                    </label>
                                    <select name="tipo"
                                        id="tipo"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="producto" <?= $producto['tipo'] === 'producto' ? 'selected' : '' ?>>
                                            Producto
                                        </option>
                                        <option value="servicio" <?= $producto['tipo'] === 'servicio' ? 'selected' : '' ?>>
                                            Servicio
                                        </option>
                                        <option value="contenido_digital" <?= $producto['tipo'] === 'contenido_digital' ? 'selected' : '' ?>>
                                            Contenido Digital
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-12 pt-4 px-4">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                    <i class="icofont-file-text mr-1"></i>
                                    Descripción
                                </label>
                                <textarea name="descripcion"
                                    id="descripcion"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Describe las características del producto..."><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                            </div>

                            <!-- Precio e IVA -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg pt-4 px-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-1 flex items-center">
                                    <i class="icofont-money mr-2"></i>
                                    Información de Precios
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                    <!-- Precio -->
                                    <div class="mb-6 pt-4 px-4">
                                        <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                            Precio Base (€) *
                                        </label>
                                        <input type="number"
                                            step="0.01"
                                            id="precio"
                                            name="precio"
                                            value="<?= $producto['precio'] ?>"
                                            required
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <p class="text-xs text-gray-500 mt-3">Precio sin IVA</p>
                                    </div>

                                    <!-- IVA -->
                                    <div class="mb-6 pt-4 px-4">
                                        <label for="iva" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                            IVA (%)
                                        </label>
                                        <input type="number"
                                            step="0.01"
                                            id="iva"
                                            name="iva"
                                            value="<?= $producto['iva'] ?>"
                                            min="0"
                                            max="100"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <p class="text-xs text-gray-500 mt-3">Porcentaje de IVA</p>
                                    </div>
                                </div>

                                <!-- Precio Final -->
                                <div class="mt-0 p-6 pb-12 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Precio Final (con IVA):</span>
                                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400" id="precioFinal">
                                            <?= number_format($producto['precio'] * (1 + $producto['iva'] / 100), 2) ?>€
                                        </span>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="flex justify-between items-center mt-10 pb-4">
                                    <!-- Botón Izquierda -->
                                    <div>
                                        <a href="productos.php"
                                            class="px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                            <i class="icofont-arrow-left mr-2"></i>
                                            Cancelar
                                        </a>
                                    </div>

                                    <!-- Botón Derecha -->
                                    <div>
                                        <button type="submit"
                                            class="px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="icofont-save mr-2"></i>
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>



                        </form>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="mt-4 mb-8 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="icofont-info-circle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Nota:</strong> Los cambios se aplicarán inmediatamente al guardar. Verifica toda la información antes de confirmar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/pages/analytics-index.init.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        // Calcular precio final automáticamente
        document.addEventListener('DOMContentLoaded', function() {
            const precioInput = document.getElementById('precio');
            const ivaInput = document.getElementById('iva');
            const precioFinalSpan = document.getElementById('precioFinal');

            function calcularPrecioFinal() {
                const precio = parseFloat(precioInput.value) || 0;
                const iva = parseFloat(ivaInput.value) || 0;
                const precioFinal = precio * (1 + iva / 100);
                precioFinalSpan.textContent = precioFinal.toFixed(2) + '€';
            }

            precioInput.addEventListener('input', calcularPrecioFinal);
            ivaInput.addEventListener('input', calcularPrecioFinal);
        });
    </script>

</body>

</html>