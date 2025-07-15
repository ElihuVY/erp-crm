<?php
include 'conexion.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $organizacion_id = intval($_POST['org']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? null;
    $tipo = $_POST['tipo'];
    $precio = floatval($_POST['precio']);
    $iva = floatval($_POST['iva']);
    $org = $_POST['org'];
    $stmt = $conn->prepare("INSERT INTO productos (organizacion_id, nombre, descripcion, tipo, precio, iva) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdd", $organizacion_id, $nombre, $descripcion, $tipo, $precio, $iva);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Producto creado exitosamente';
    } else {
        $_SESSION['error'] = 'Error al crear el producto';
    }

    header("Location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <style>
        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }

        .py-30 {
            padding-left: 15px;
        }
    </style>

    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Css -->
    <!-- Main Css -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <?php
    include 'menu.php';
    ?>

    <div class="xl:w-full">
        <main class="pt-[90px] px-4 pb-16 lg:ms-[260px]">
            <!-- Header con breadcrumbs -->
            <div class="mb-6">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <a href="productos.php" class="flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                            <i class="icofont-arrow-left text-lg mr-2"></i>
                            <span class="font-medium">Volver a Productos</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Formulario Principal -->
                <div class="xl:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-slate-700/10 overflow-hidden">
                        <!-- Header del Card -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 rounded-t-2xl">
                            <div class="flex flex-col space-y-4">
                                <!-- Header Title -->
                                <div class="flex items-center space-x-2">
                                    <div class="bg-white rounded-full p-2 shadow-lg">
                                        <i class="icofont-plus-circle text-blue-600 text-2xl"></i>
                                    </div>
                                    <h1 class="text-3xl font-bold text-black">Crear Nuevo Producto</h1>
                                </div>

                                <!-- Subheader Info -->
                                <div class="flex items-center space-x-4 mt-2">

                                    <div class="border-l-4 border-white/30 pl-4">
                                        <h2 class="text-xl font-semibold text-black">Información del Producto</h2>
                                        <p class="text-black text-sm mt-1">Complete los datos del nuevo producto</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario -->
                        <div class="p-8 pb-15">
                            <form method="POST" id="producto-form">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-y-12 lg:gap-x-16">

                                    <!-- Columna Izquierda -->
                                    <div class="space-y-8">
                                        <!-- Nombre del Producto -->
                                        <div class="form-group mb-6 pt-4 px-4">
                                            <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                                <i class="icofont-tag text-slate-500"></i>
                                                <span>Nombre del Producto <span class="text-red-500">*</span></span>
                                            </label>
                                            <input
                                                type="text"
                                                name="nombre"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-slate-400 bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200"
                                                placeholder="Ej: Producto Modelo "
                                                required>
                                        </div>
                                        <div class="form-group mb-6 pt-4 px-4">
                                            <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                                <i class="icofont-category text-slate-500"></i>
                                                <span>Escoge Organización <span class="text-red-500">*</span></span>
                                            </label>
                                            <div class="relative">
                                                <select
                                                    name="org"
                                                    class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200"
                                                    required>
                                                    <option value="">Seleccionar organización...</option>
                                                    <?php
                                                    // Get organizations associated with current user
                                                    $usuario_id = $_SESSION['usuario_id'];
                                                    $sql = "SELECT o.id, o.nombre 
                                                           FROM organizaciones o
                                                           INNER JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
                                                           WHERE uo.usuario_id = ?";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bind_param("i", $usuario_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" .
                                                            htmlspecialchars($row['nombre']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <i class="icofont-rounded-down absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>

                                        <!-- Tipo de Producto -->
                                        <div class="form-group mb-6 pt-4 px-4">
                                            <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                                <i class="icofont-category text-slate-500"></i>
                                                <span>Tipo de Producto <span class="text-red-500">*</span></span>
                                            </label>
                                            <div class="relative">
                                                <select
                                                    name="tipo"
                                                    class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200"
                                                    required>
                                                    <option value="">Seleccionar tipo...</option>
                                                    <option value="producto">📦 Producto Físico</option>
                                                    <option value="servicio">🛠️ Servicio</option>
                                                    <option value="contenido_digital">💾 Contenido Digital</option>
                                                </select>
                                                <i class="icofont-rounded-down absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>
                                        <!-- Precios -->
                                        <div class="grid grid-cols-2 gap-6 pt-4 px-4">
                                            <!-- Precio -->
                                            <div class="form-group mb-6 pt-4 px-4" >
                                                <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                                    <i class="icofont-euro text-slate-500"></i>
                                                    <span>Precio (€) <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    name="precio"
                                                    class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200"
                                                    placeholder="0.00"
                                                    min="0"
                                                    required>
                                            </div>
                                            <!-- IVA -->
                                            <div class="form-group mb-6 pt-4 px-4">
                                                <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                                    <i class="icofont-percentage text-slate-500"></i>
                                                    <span>IVA (%)</span>
                                                </label>
                                                <select
                                                    name="iva"
                                                    class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200">
                                                    <option value="0">0% - Exento</option>
                                                    <option value="4">4% - Tipo superreducido</option>
                                                    <option value="10">10% - Tipo reducido</option>
                                                    <option value="21" selected>21% - Tipo general</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Columna Derecha -->
                                    <div class="py-30 pt-4 px-4">
                                        <div class="space-y-6 pt-4 px-4">
                                            <!-- Descripción -->
                                            <div class="form-group">
                                                <label class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                                    <i class="icofont-file-text text-slate-500"></i>
                                                    <span>Descripción</span>
                                                </label>
                                                <textarea
                                                    name="descripcion"
                                                    rows="8"
                                                    class="w-full px-4 py-3 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none placeholder-slate-400 bg-white dark:bg-gray-700 text-slate-900 dark:text-slate-200"
                                                    placeholder="Describe las características principales del producto..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="flex items-center justify-between pt-8 border-t border-slate-200 dark:border-gray-600 mt-8 space-y-4 px-4">
                                    <a href="productos.php" class="inline-flex items-center space-x-2 px-6 py-3 border border-slate-300 dark:border-gray-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors font-medium">
                                        <i class="icofont-close-line"></i>
                                        <style>
                                            .button-padding {
                                                padding: 5px;
                                            }
                                        </style>
                                        <span class="button-padding">Cancelar</span>
                                    </a>

                                    <button type="submit" class="inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <i class="icofont-save"></i>
                                        <span class="button-padding">Guardar Producto</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Vista previa y ayuda -->
                <div class="xl:col-span-1">
                    <!-- Vista previa del precio -->
                    <div class="py-30">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-slate-700/10 mb-6">
                            <div class="border-b border-slate-200 dark:border-gray-700 p-4">
                                <h4 class="flex items-center space-x-2 text-sm font-medium text-slate-700 dark:text-slate-300 mb-0">
                                    <i class="icofont-calculator text-slate-500"></i>
                                    <span>Vista Previa de Precios</span>
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-slate-400">Precio base:</span>
                                        <span class="font-medium text-slate-900 dark:text-slate-200" id="precio-base">0,00 €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-slate-400">IVA:</span>
                                        <span class="font-medium text-slate-900 dark:text-slate-200" id="precio-iva">0,00 €</span>
                                    </div>
                                    <div class="border-t border-slate-300 dark:border-gray-600 pt-3 flex justify-between">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">Total:</span>
                                        <span class="font-bold text-blue-600 text-lg" id="precio-total">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Tips Card -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl shadow-sm">
                            <div class="p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="icofont-info-circle text-blue-600 text-lg mt-0.5"></i>
                                    <div>
                                        <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-2"> Consejos para crear productos</h4>
                                        <ul class="text-sm text-blue-800 dark:text-blue-400 space-y-1">
                                            <li>• Usa nombres descriptivos y únicos para cada producto</li>
                                            <li>• Incluye detalles importantes en la descripción</li>
                                            <li>• Verifica que el precio y el IVA sean correctos</li>
                                            <li>• Selecciona el tipo correcto según tu producto</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JAVASCRIPTS -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/pages/analytics-index.init.js"></script>
    <script src="assets/js/app.js"></script>

    <!-- Custom JavaScript para el formulario -->
    <script>
        // Calculadora de precios en tiempo real
        function actualizarPrecios() {
            const precioInput = document.querySelector('input[name="precio"]');
            const ivaSelect = document.querySelector('select[name="iva"]');

            const precio = parseFloat(precioInput.value) || 0;
            const iva = parseFloat(ivaSelect.value) || 0;

            const montoIva = precio * (iva / 100);
            const total = precio + montoIva;

            document.getElementById('precio-base').textContent = precio.toFixed(2) + ' €';
            document.getElementById('precio-iva').textContent = montoIva.toFixed(2) + ' €';
            document.getElementById('precio-total').textContent = total.toFixed(2) + ' €';
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const precioInput = document.querySelector('input[name="precio"]');
            const ivaSelect = document.querySelector('select[name="iva"]');

            if (precioInput) precioInput.addEventListener('input', actualizarPrecios);
            if (ivaSelect) ivaSelect.addEventListener('change', actualizarPrecios);

            // Validación del formulario
            const form = document.getElementById('producto-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nombre = document.querySelector('input[name="nombre"]').value.trim();
                    const tipo = document.querySelector('select[name="tipo"]').value;
                    const precio = document.querySelector('input[name="precio"]').value;

                    if (!nombre || !tipo || !precio) {
                        e.preventDefault();
                        alert('Por favor, complete todos los campos obligatorios.');
                        return false;
                    }

                    if (parseFloat(precio) < 0) {
                        e.preventDefault();
                        alert('El precio no puede ser negativo.');
                        return false;
                    }
                });
            }

            // Inicializar calculadora
            actualizarPrecios();
        });
    </script>

</body>

</html>