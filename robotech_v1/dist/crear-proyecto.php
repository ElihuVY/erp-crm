<?php
include 'conexion.php';

// Obtener clientes y presupuestos aceptados
$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
$presupuestos = $conn->query("SELECT id, importe FROM presupuestos WHERE estado = 'aceptado' ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $presupuesto_id = $_POST['presupuesto_id'] !== '' ? $_POST['presupuesto_id'] : null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin = $_POST['fecha_fin'] ?: null;

    $stmt = $conn->prepare("INSERT INTO proyectos (cliente_id, presupuesto_id, nombre, descripcion, estado, fecha_inicio, fecha_fin, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssss", $cliente_id, $presupuesto_id, $nombre, $descripcion, $estado, $fecha_inicio, $fecha_fin);
    $stmt->execute();

    header("Location: proyectos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <style>
        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }

        .form-group {
            @apply mb-6;
        }

        .form-label {
            @apply block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100;
        }

        .form-select {
            @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none;
        }

        .form-textarea {
            @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 resize-y;
        }

        .btn-primary {
            @apply bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200;
        }

        .btn-secondary {
            @apply bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium px-6 py-3 rounded-lg transition-all duration-200;
        }

        .card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden;
        }

        .card-header {
            @apply bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700;
        }

        .card-body {
            @apply p-6;
        }

        .form-grid {
            @apply grid grid-cols-1 md:grid-cols-2 gap-6;
        }

        .icon-wrapper {
            @apply flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 mr-3;
        }

        .step-indicator {
            @apply flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold mr-3;
        }

        .alert {
            @apply p-4 rounded-lg border-l-4 mb-6;
        }

        .alert-info {
            @apply bg-blue-50 border-blue-400 text-blue-800 dark:bg-blue-900 dark:text-blue-300;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
        }

        .status-planned {
            @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300;
        }

        .status-in-progress {
            @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300;
        }

        .status-completed {
            @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300;
        }

        .status-cancelled {
            @apply bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300;
        }
    </style>
    <meta charset="UTF-8">
    <title>Crear Proyecto - Robotech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Css -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">

    <!-- Include menu -->
    <?php include 'menu.php'; ?>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:ms-[260px] py-12">
                <div class="max-w-5xl mx-auto">

                    <!-- Professional Form Header -->
                    <div class="mb-6 pt-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <span class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                                        <i class="icofont-folder-open text-2xl text-blue-600 dark:text-blue-400"></i>
                                    </span>
                                    Crear Nuevo Proyecto
                                </h1>
                                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                                    Complete el formulario para crear un nuevo proyecto
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form Container -->
                    <form method="POST" class="space-y-8">

                        <!-- Client Information Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden max-w-3xl mx-auto">
                            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <i class="icofont-building text-blue-600 text-2xl mr-3"></i>
                                    Información del Cliente
                                </h2>
                            </div>

                            <div class="p-6 pb-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Client Selection -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="icofont-user text-blue-600 mr-2"></i>
                                            Cliente
                                        </label>
                                        <select name="cliente_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800" required>
                                            <option value="">Seleccione un cliente</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- Budget Selection -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="icofont-money text-blue-600 mr-2"></i>
                                            Presupuesto Asociado
                                            <span class="text-xs text-gray-500">(Opcional)</span>
                                        </label>
                                        <select name="presupuesto_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800" id="presupuesto_select">
                                            <option value="">Sin presupuesto asociado</option>
                                            <?php foreach ($presupuestos as $p): ?>
                                                <option value="<?= $p['id'] ?>" data-amount="<?= $p['importe'] ?>">
                                                    Presupuesto #<?= $p['id'] ?> - <?= number_format($p['importe'], 2) ?>€
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Project Details -->
                                <div class="mt-8">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="icofont-edit text-blue-600 mr-2"></i>
                                        Nombre del Proyecto
                                    </label>
                                    <input type="text" name="nombre" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800" required placeholder="Ingrese el nombre del proyecto">
                                </div>
                                <div class="mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="icofont-file-text text-blue-600 mr-2"></i>
                                        Descripción del Proyecto
                                    </label>
                                    <textarea name="descripcion" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800" placeholder="Describa los objetivos y alcance del proyecto..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Project Status & Timeline -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <i class="icofont-calendar text-blue-600 text-2xl mr-3"></i>
                                    Estado y Cronograma
                                </h2>
                            </div>

                            <div class="p-6">
                                <!-- Project Status -->
                                <div class="mb-8">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="icofont-check-circled text-blue-600 mr-2"></i>
                                        Estado del Proyecto
                                    </label>
                                    <select name="estado" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800" required id="estado_select">
                                        <option value="planificado">📋 Planificado</option>
                                        <option value="en curso">🚀 En Progreso</option>
                                        <option value="completado">✅ Completado</option>
                                        <option value="cancelado">❌ Cancelado</option>
                                    </select>
                                </div>

                                <!-- Project Timeline -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="icofont-calendar text-blue-600 mr-2"></i>
                                            Fecha de Inicio
                                        </label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="icofont-calendar text-blue-600 mr-2"></i>
                                            Fecha de Finalización
                                        </label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-16 mt-6 pb-6">
                            <div class="flex items-center">
                                <a href="proyectos.php" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <i class="icofont-arrow-left mr-2"></i>
                                    Volver
                                </a>
                            </div>

                            <div class="flex items-center space-x-4">
                                <button type="button" onclick="limpiarFormulario()" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <i class="icofont-refresh mr-2"></i>
                                    Limpiar
                                </button>

                                <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition duration-150 ease-in-out">
                                    <i class="icofont-check mr-2"></i>
                                    Crear Proyecto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
        // Initialize flatpickr for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr('#fecha_inicio, #fecha_fin', {
                dateFormat: "Y-m-d",
                locale: "es",
                minDate: "today"
            });

            // Update resumen on load
            updateProjectSummary();
        });

        // Handle presupuesto selection
        document.getElementById('presupuesto_select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const presupuestoInfo = document.getElementById('presupuesto-info');
            const presupuestoAmount = document.getElementById('presupuesto-amount');

            if (this.value && selectedOption.dataset.amount) {
                presupuestoAmount.textContent = new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(selectedOption.dataset.amount);
                presupuestoInfo.classList.remove('hidden');
            } else {
                presupuestoInfo.classList.add('hidden');
            }

            updateProjectSummary();
        });

        // Handle estado selection
        document.getElementById('estado_select').addEventListener('change', function() {
            const estadoInfo = document.getElementById('estado-info');
            const estadoDescription = document.getElementById('estado-description');

            const descriptions = {
                'planificado': 'El proyecto está en fase de planificación',
                'en curso': 'El proyecto está actualmente en desarrollo',
                'completado': 'El proyecto ha sido finalizado exitosamente',
                'cancelado': 'El proyecto ha sido cancelado'
            };

            const colors = {
                'planificado': 'bg-yellow-50 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
                'en curso': 'bg-blue-50 dark:bg-blue-900 text-blue-800 dark:text-blue-300',
                'completado': 'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-300',
                'cancelado': 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-300'
            };

            estadoInfo.className = `mt-2 p-3 rounded-lg ${colors[this.value] || colors['planificado']}`;
            estadoDescription.textContent = descriptions[this.value] || descriptions['planificado'];

            updateProjectSummary();
        });

        // Handle date changes to calculate duration
        function calculateDuration() {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            const duracionInfo = document.getElementById('duracion-info');
            const duracionDays = document.getElementById('duracion-days');

            if (fechaInicio && fechaFin) {
                const inicio = new Date(fechaInicio);
                const fin = new Date(fechaFin);
                const diffTime = Math.abs(fin - inicio);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 0) {
                    duracionDays.textContent = diffDays;
                    duracionInfo.classList.remove('hidden');
                } else {
                    duracionInfo.classList.add('hidden');
                }
            } else {
                duracionInfo.classList.add('hidden');
            }
        }

        document.getElementById('fecha_inicio').addEventListener('change', calculateDuration);
        document.getElementById('fecha_fin').addEventListener('change', calculateDuration);

        // Update project summary
        function updateProjectSummary() {
            const clienteSelect = document.querySelector('select[name="cliente_id"]');
            const presupuestoSelect = document.getElementById('presupuesto_select');
            const estadoSelect = document.getElementById('estado_select');

            // Update client
            const resumenCliente = document.getElementById('resumen-cliente');
            if (clienteSelect.value) {
                resumenCliente.textContent = clienteSelect.options[clienteSelect.selectedIndex].text;
            } else {
                resumenCliente.textContent = '-';
            }

            // Update budget
            const resumenPresupuesto = document.getElementById('resumen-presupuesto');
            if (presupuestoSelect.value) {
                const selectedOption = presupuestoSelect.options[presupuestoSelect.selectedIndex];
                if (selectedOption.dataset.amount) {
                    resumenPresupuesto.textContent = new Intl.NumberFormat('es-ES', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(selectedOption.dataset.amount);
                }
            } else {
                resumenPresupuesto.textContent = 'Sin presupuesto';
            }

            // Update status
            const resumenEstado = document.getElementById('resumen-estado');
            const estadoIcons = {
                'planificado': '📋',
                'en curso': '🚀',
                'completado': '✅',
                'cancelado': '❌'
            };
            resumenEstado.textContent = estadoIcons[estadoSelect.value] || '📋';
        }

        // Add event listeners for summary updates
        document.querySelector('select[name="cliente_id"]').addEventListener('change', updateProjectSummary);

        // Clear form function
        function limpiarFormulario() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos?')) {
                document.querySelector('form').reset();
                document.getElementById('presupuesto-info').classList.add('hidden');
                document.getElementById('duracion-info').classList.add('hidden');
                updateProjectSummary();
            }
        }

        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const cliente = document.querySelector('select[name="cliente_id"]').value;

            if (!nombre) {
                e.preventDefault();
                alert('Por favor, ingrese el nombre del proyecto');
                return;
            }

            if (!cliente) {
                e.preventDefault();
                alert('Por favor, seleccione un cliente');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="icofont-spinner animate-spin mr-2"></i>Creando proyecto...';
            submitBtn.disabled = true;
        });
    </script>

</body>

</html>