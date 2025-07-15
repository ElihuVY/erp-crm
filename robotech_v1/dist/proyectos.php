<?php
//falta mejorar la vista  ya se guarda en bdd cuando se arrastra entre columnas, no se completa hasta que hayn terminado todas las tareas pendientes
session_start();
include 'conexion.php';

// Actualizar estado mediante AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_estado') {
    $proyecto_id = intval($_POST['proyecto_id']);
    $nuevo_estado = $conn->real_escape_string($_POST['nuevo_estado']);

    // Validar que no se pueda marcar como completado si hay tareas pendientes
    if ($nuevo_estado === 'completado') {
        $tareas_abiertas = $conn->query("SELECT COUNT(*) AS abiertas FROM tareas WHERE proyecto_id = $proyecto_id AND estado != 'finalizada'")->fetch_assoc();
        if ($tareas_abiertas['abiertas'] > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede marcar como completado: hay tareas sin finalizar.']);
            exit();
        }
    }

    $result = $conn->query("UPDATE proyectos SET estado = '$nuevo_estado' WHERE id = $proyecto_id");

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar estado']);
    }
    exit();
}

$buscar = $_GET['buscar'] ?? '';
$cliente_id = $_GET['cliente_id'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';

$condiciones = [];
if ($buscar !== '') {
    $condiciones[] = "p.nombre LIKE '%" . $conn->real_escape_string($buscar) . "%'";
}
if ($cliente_id !== '') {
    $condiciones[] = "p.cliente_id = " . intval($cliente_id);
}
if ($fecha_inicio !== '') {
    $condiciones[] = "p.fecha_inicio >= '" . $conn->real_escape_string($fecha_inicio) . "'";
}

$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$query = "SELECT p.*, c.nombre AS cliente FROM proyectos p JOIN clientes c ON p.cliente_id = c.id $where ORDER BY p.id DESC";
$result = $conn->query($query);
$todos_proyectos = $result->fetch_all(MYSQLI_ASSOC);

// Organizar proyectos por estado
$proyectos = [
    'planificado' => [],
    'en curso' => [],
    'completado' => [],
    'cancelado' => []
];

foreach ($todos_proyectos as $proyecto) {
    $proyectos[$proyecto['estado']][] = $proyecto;
}

$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

// Obtener progreso de tareas para cada proyecto
$progreso = [];
$tareas_proyecto = [];
$ids_proyectos = array_column($todos_proyectos, 'id');
if (count($ids_proyectos) > 0) {
    $ids = implode(',', $ids_proyectos);
    $tareas = $conn->query("SELECT * FROM tareas WHERE proyecto_id IN ($ids)")->fetch_all(MYSQLI_ASSOC);
    foreach ($tareas as $t) {
        $pid = $t['proyecto_id'];
        $tareas_proyecto[$pid][] = $t;
        $progreso[$pid]['total'] = ($progreso[$pid]['total'] ?? 0) + 1;
        if ($t['estado'] === 'finalizada') {
            $progreso[$pid]['completadas'] = ($progreso[$pid]['completadas'] ?? 0) + 1;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Proyectos </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Gestión de Proyectos" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <style>
        .kanban-column {
            min-height: 500px;
            min-width: 300px;
            flex: 0 0 300px;
        }

        .proyecto-card {
            cursor: grab;
        }

        .proyecto-card:active {
            cursor: grabbing;
        }

        .column-planificado .column-header {
            background-color: #e5edff;
        }

        .column-en-curso .column-header {
            background-color: #fff8e5;
        }

        .column-completado .column-header {
            background-color: #e5ffe7;
        }

        .column-cancelado .column-header {
            background-color: #ffe5e5;
        }

        .border-planificado {
            border-left-color: #3b82f6;
        }

        .border-en-curso {
            border-left-color: #f59e0b;
        }

        .border-completado {
            border-left-color: #10b981;
        }

        .border-cancelado {
            border-left-color: #ef4444;
        }

        .sortable-ghost {
            opacity: 0.5;
            background-color: #f3f4f6;
        }
    </style>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <?php include 'menu.php'; ?>

    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center w-full">
                <div class="w-full">
                    <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
                        <div class="max-w-full mx-auto">
                            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                                <h1 class="text-2xl font-bold">📋 Proyectos</h1>
                                <div class="flex gap-2">
                                    <a href="crear-proyecto.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"> <i class="icofont-folder-open text-2xl text-white-600 dark:text-blue-400"></i> Crear proyecto</a>
                                    
                                    <a href="proyectos2.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"><i class="icofont-eye text-2xl"></i> Cambiar Vista</a>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['error_estado'])): ?>
                                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                                    <?= $_SESSION['error_estado'];
                                    unset($_SESSION['error_estado']); ?>
                                </div>
                            <?php endif; ?>

                            <div id="estado-alert" class="hidden bg-red-100 text-red-700 px-4 py-2 rounded mb-4"></div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                                <input type="text"
                                    id="buscar"
                                    placeholder="Buscar por nombre"
                                    value="<?= htmlspecialchars($buscar) ?>"
                                    class="p-2 border rounded"
                                    onkeyup="setTimeout(function() { filtrar(); }, 2000)">
                                <?php
                                $cliente_nombre = '';
                                if ($cliente_id) {
                                    foreach ($clientes as $c) {
                                        if ($c['id'] == $cliente_id) {
                                            $cliente_nombre = $c['nombre'];
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <input type="text" id="cliente_buscar" class="p-2 border rounded"
                                    placeholder="Buscar cliente..."
                                    list="lista_clientes"
                                    value="<?= htmlspecialchars($cliente_nombre) ?>"
                                    onchange="actualizarClienteId(this.value)">
                                <input type="hidden" id="cliente_id" value="<?= $cliente_id ?>">
                                <datalist id="lista_clientes">
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= htmlspecialchars($c['nombre']) ?>" data-id="<?= $c['id'] ?>">
                                        <?php endforeach; ?>
                                </datalist>
                                <script>
                                    function actualizarClienteId(nombreCliente) {
                                        const options = document.getElementById('lista_clientes').options;
                                        for (let option of options) {
                                            if (option.value === nombreCliente) {
                                                document.getElementById('cliente_id').value = option.dataset.id;
                                                filtrar();
                                                return;
                                            }
                                        }
                                        document.getElementById('cliente_id').value = '';
                                        filtrar();
                                    }
                                </script>
                                <div class="flex gap-2">
                                    <input type="date" id="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="p-2 border rounded flex-1" onchange="filtrar()">
                                    <button onclick="limpiarFiltros()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                        🔄 Limpiar filtros
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-row gap-4 overflow-x-auto pb-4 justify-start kanban-container sm:justify-center">
                                <!-- Columnas Kanban aquí -->

                                <!-- Columna: Planificado -->
                                <div class="kanban-column column-planificado min-w-[300px] w-full lg:w-1/4 bg-white rounded-lg shadow-md flex flex-col">
                                    <div class="column-header p-3 text-center font-bold text-gray-800 rounded-t-lg border-b">
                                        <div class="flex items-center justify-center">
                                            Planificado
                                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                <?= count($proyectos['planificado']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="planificado" class="flex-1 p-3 overflow-y-auto" data-estado="planificado">
                                        <?php if (empty($proyectos['planificado'])): ?>
                                            <p class="text-gray-500 text-center italic p-4">No hay proyectos planificados</p>
                                        <?php else: ?>
                                            <?php foreach ($proyectos['planificado'] as $proyecto): ?>
                                                <div class="proyecto-card mb-3 bg-white rounded border-l-4 border-planificado shadow p-3" data-id="<?= $proyecto['id'] ?>">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="font-medium text-gray-800"><?= htmlspecialchars($proyecto['nombre']) ?></h3>
                                                        <span class="text-xs text-gray-500">#<?= $proyecto['id'] ?></span>
                                                    </div>
                                                    <div class="text-xs text-gray-600 mt-1">Cliente: <?= htmlspecialchars($proyecto['cliente']) ?></div>
                                                    <?php
                                                    $total = $progreso[$proyecto['id']]['total'] ?? 0;
                                                    $completadas = $progreso[$proyecto['id']]['completadas'] ?? 0;
                                                    $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span>Progreso</span>
                                                            <span><?= $porcentaje ?>% (<?= $completadas ?>/<?= $total ?>)</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex mt-3 gap-1 text-xs">
                                                        <a href="ver-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-blue-600 hover:underline">Ver</a>
                                                        <span class="text-gray-400">|</span>
                                                        <a href="editar-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-orange-600 hover:underline">Editar</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Columna: En Curso -->
                                <div class="kanban-column column-en-curso min-w-[300px] w-full lg:w-1/4 bg-white rounded-lg shadow-md flex flex-col">
                                    <div class="column-header p-3 text-center font-bold text-gray-800 rounded-t-lg border-b">
                                        <div class="flex items-center justify-center">
                                            En Curso
                                            <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                <?= count($proyectos['en curso']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="en-curso" class="flex-1 p-3 overflow-y-auto" data-estado="en curso">
                                        <?php if (empty($proyectos['en curso'])): ?>
                                            <p class="text-gray-500 text-center italic p-4">No hay proyectos en curso</p>
                                        <?php else: ?>
                                            <?php foreach ($proyectos['en curso'] as $proyecto): ?>
                                                <div class="proyecto-card mb-3 bg-white rounded border-l-4 border-en-curso shadow p-3" data-id="<?= $proyecto['id'] ?>">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="font-medium text-gray-800"><?= htmlspecialchars($proyecto['nombre']) ?></h3>
                                                        <span class="text-xs text-gray-500">#<?= $proyecto['id'] ?></span>
                                                    </div>
                                                    <div class="text-xs text-gray-600 mt-1">Cliente: <?= htmlspecialchars($proyecto['cliente']) ?></div>
                                                    <?php
                                                    $total = $progreso[$proyecto['id']]['total'] ?? 0;
                                                    $completadas = $progreso[$proyecto['id']]['completadas'] ?? 0;
                                                    $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span>Progreso</span>
                                                            <span><?= $porcentaje ?>% (<?= $completadas ?>/<?= $total ?>)</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="bg-yellow-500 h-1.5 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex mt-3 gap-1 text-xs">
                                                        <a href="ver-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-blue-600 hover:underline">Ver</a>
                                                        <span class="text-gray-400">|</span>
                                                        <a href="editar-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-orange-600 hover:underline">Editar</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Columna: Completado -->
                                <div class="kanban-column column-completado min-w-[300px] w-full lg:w-1/4 bg-white rounded-lg shadow-md flex flex-col">
                                    <div class="column-header p-3 text-center font-bold text-gray-800 rounded-t-lg border-b">
                                        <div class="flex items-center justify-center">
                                            Completado
                                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                <?= count($proyectos['completado']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="completado" class="flex-1 p-3 overflow-y-auto" data-estado="completado">
                                        <?php if (empty($proyectos['completado'])): ?>
                                            <p class="text-gray-500 text-center italic p-4">No hay proyectos completados</p>
                                        <?php else: ?>
                                            <?php foreach ($proyectos['completado'] as $proyecto): ?>
                                                <div class="proyecto-card mb-3 bg-white rounded border-l-4 border-completado shadow p-3" data-id="<?= $proyecto['id'] ?>">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="font-medium text-gray-800"><?= htmlspecialchars($proyecto['nombre']) ?></h3>
                                                        <span class="text-xs text-gray-500">#<?= $proyecto['id'] ?></span>
                                                    </div>
                                                    <div class="text-xs text-gray-600 mt-1">Cliente: <?= htmlspecialchars($proyecto['cliente']) ?></div>
                                                    <?php
                                                    $total = $progreso[$proyecto['id']]['total'] ?? 0;
                                                    $completadas = $progreso[$proyecto['id']]['completadas'] ?? 0;
                                                    $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span>Progreso</span>
                                                            <span><?= $porcentaje ?>% (<?= $completadas ?>/<?= $total ?>)</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="bg-green-500 h-1.5 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex mt-3 gap-1 text-xs">
                                                        <a href="ver-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-blue-600 hover:underline">Ver</a>
                                                        <span class="text-gray-400">|</span>
                                                        <a href="editar-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-orange-600 hover:underline">Editar</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Columna: Cancelado -->
                                <div class="kanban-column column-cancelado min-w-[300px] w-full lg:w-1/4 bg-white rounded-lg shadow-md flex flex-col">
                                    <div class="column-header p-3 text-center font-bold text-gray-800 rounded-t-lg border-b">
                                        <div class="flex items-center justify-center">
                                            Cancelado
                                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                <?= count($proyectos['cancelado']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="cancelado" class="flex-1 p-3 overflow-y-auto" data-estado="cancelado">
                                        <?php if (empty($proyectos['cancelado'])): ?>
                                            <p class="text-gray-500 text-center italic p-4">No hay proyectos cancelados</p>
                                        <?php else: ?>
                                            <?php foreach ($proyectos['cancelado'] as $proyecto): ?>
                                                <div class="proyecto-card mb-3 bg-white rounded border-l-4 border-cancelado shadow p-3" data-id="<?= $proyecto['id'] ?>">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="font-medium text-gray-800"><?= htmlspecialchars($proyecto['nombre']) ?></h3>
                                                        <span class="text-xs text-gray-500">#<?= $proyecto['id'] ?></span>
                                                    </div>
                                                    <div class="text-xs text-gray-600 mt-1">Cliente: <?= htmlspecialchars($proyecto['cliente']) ?></div>
                                                    <?php
                                                    $total = $progreso[$proyecto['id']]['total'] ?? 0;
                                                    $completadas = $progreso[$proyecto['id']]['completadas'] ?? 0;
                                                    $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span>Progreso</span>
                                                            <span><?= $porcentaje ?>% (<?= $completadas ?>/<?= $total ?>)</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="bg-red-500 h-1.5 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex mt-3 gap-1 text-xs">
                                                        <a href="ver-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-blue-600 hover:underline">Ver</a>
                                                        <span class="text-gray-400">|</span>
                                                        <a href="editar-proyecto.php?id=<?= $proyecto['id'] ?>" class="text-orange-600 hover:underline">Editar</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </main>
            </div>
        </div>

        
    </div>
    </div>

    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
    <script src="assets/js/app.js"></script>
    <!-- Incluir Sortable.js para drag & drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <script>
        // Función para filtrar proyectos
        function filtrar() {
            const buscar = document.getElementById('buscar').value;
            const cliente_id = document.getElementById('cliente_id').value;
            const fecha_inicio = document.getElementById('fecha_inicio').value;

            const params = new URLSearchParams({
                buscar,
                cliente_id,
                fecha_inicio
            });
            window.location.href = 'proyecto2.php?' + params.toString();
        }

        // Función para limpiar todos los filtros
        function limpiarFiltros() {
            document.getElementById('buscar').value = '';
            document.getElementById('cliente_buscar').value = '';
            document.getElementById('cliente_id').value = '';
            document.getElementById('fecha_inicio').value = '';
            window.location.href = 'proyecto2.php';
        }

        // Inicializar Sortable en cada columna
        document.addEventListener('DOMContentLoaded', function() {
            const columnas = ['planificado', 'en-curso', 'completado', 'cancelado'];

            columnas.forEach(columnaId => {
                const columna = document.getElementById(columnaId);

                new Sortable(columna, {
                    group: 'proyectos',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const proyectoId = evt.item.getAttribute('data-id');
                        const nuevoEstado = evt.to.getAttribute('data-estado');

                        // Actualizar estado en la base de datos mediante AJAX
                        actualizarEstado(proyectoId, nuevoEstado);
                    }
                });
            });
        });

        // Función para actualizar el estado mediante AJAX
        function actualizarEstado(proyectoId, nuevoEstado) {
            const formData = new FormData();
            formData.append('action', 'update_estado');
            formData.append('proyecto_id', proyectoId);
            formData.append('nuevo_estado', nuevoEstado);

            fetch('proyecto2.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // Mostrar mensaje de error
                        const alertElement = document.getElementById('estado-alert');
                        alertElement.textContent = data.message;
                        alertElement.classList.remove('hidden');

                        // Recargar la página después de mostrar el error
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.reload();
                });
        }
    </script>

</body>

</html>

<style>
    .kanban-column {
        min-height: 500px;
        min-width: 300px;
        flex: 0 0 300px;
    }

    .proyecto-card {
        cursor: grab;
    }

    .proyecto-card:active {
        cursor: grabbing;
    }

    .column-planificado .column-header {
        background-color: #e5edff;
    }

    .column-en-curso .column-header {
        background-color: #fff8e5;
    }

    .column-completado .column-header {
        background-color: #e5ffe7;
    }

    .column-cancelado .column-header {
        background-color: #ffe5e5;
    }

    .border-planificado {
        border-left-color: #3b82f6;
    }

    .border-en-curso {
        border-left-color: #f59e0b;
    }

    .border-completado {
        border-left-color: #10b981;
    }

    .border-cancelado {
        border-left-color: #ef4444;
    }

    .sortable-ghost {
        opacity: 0.5;
        background-color: #f3f4f6;
    }

    @media (max-width: 768px) {
        .kanban-column {
            min-width: 280px;
            flex: 0 0 280px;
        }
    }
</style>