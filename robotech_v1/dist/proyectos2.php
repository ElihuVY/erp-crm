<?php
include 'conexion.php';

// Cambiar estado si se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proyecto_id'], $_POST['nuevo_estado'])) {
    $proyecto_id = intval($_POST['proyecto_id']);
    $nuevo_estado = $conn->real_escape_string($_POST['nuevo_estado']);

    if ($nuevo_estado === 'completado') {
        $tareas_abiertas = $conn->query("SELECT COUNT(*) AS abiertas FROM tareas WHERE proyecto_id = $proyecto_id AND estado != 'finalizada'")->fetch_assoc();
        if ($tareas_abiertas['abiertas'] > 0) {
            $_SESSION['error_estado'] = "❌ No se puede marcar como completado: hay tareas sin finalizar.";
            header("Location: proyectos.php");
            exit();
        }
    }

    $conn->query("UPDATE proyectos SET estado = '$nuevo_estado' WHERE id = $proyecto_id");
    header("Location: proyectos.php");
    exit();
}

$buscar = $_GET['buscar'] ?? '';
$estado = $_GET['estado'] ?? '';
$cliente_id = $_GET['cliente_id'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';

$condiciones = [];
if ($buscar !== '') {
    $condiciones[] = "p.nombre LIKE '%" . $conn->real_escape_string($buscar) . "%'";
}
if ($estado !== '') {
    $condiciones[] = "p.estado = '" . $conn->real_escape_string($estado) . "'";
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
$proyectos = $result->fetch_all(MYSQLI_ASSOC);

$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

$progreso = [];
$tareas_proyecto = [];
$ids_proyectos = array_column($proyectos, 'id');
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
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Proyectos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">

    <link rel="shortcut icon" href="assets/images/favicon.ico" />


    <script>
        function filtrar() {
            const buscar = document.getElementById('buscar').value;
            const estado = document.getElementById('estado').value;
            const cliente_id = document.getElementById('cliente_id').value;
            const fecha_inicio = document.getElementById('fecha_inicio').value;

            const params = new URLSearchParams({
                buscar,
                estado,
                cliente_id,
                fecha_inicio
            });
            window.location.href = 'proyectos.php?' + params.toString();
        }

        function toggleTareas(id) {
            const fila = document.getElementById('tareas-' + id);
            fila.classList.toggle('hidden');
        }
    </script>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">


    <?php
    include 'menu.php';
    ?>


    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center py-4 w-full">
                <div class="w-full">
                    <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">



                        <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">

                            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                                <h1 class="text-2xl font-bold">📁 Gestión de Proyectos</h1>
                                <div class="flex gap-2">
                                    <a href="crear-proyecto.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"> <i class="icofont-folder-open text-2xl text-white-600 dark:text-blue-400"></i> Crear proyecto</a>
                                    <a href="proyectos.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"><i class="icofont-eye text-2xl"></i> Cambiar Vista</a>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['error_estado'])): ?>
                                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                                    <?= $_SESSION['error_estado'];
                                    unset($_SESSION['error_estado']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                                <input type="text" id="buscar" placeholder="Buscar por nombre" value="<?= htmlspecialchars($buscar) ?>" class="p-2 border rounded" oninput="filtrar()">
                                <select id="estado" class="p-2 border rounded" onchange="filtrar()">
                                    <option value="">Todos los estados</option>
                                    <option value="planificado" <?= $estado === 'planificado' ? 'selected' : '' ?>>Planificado</option>
                                    <option value="en curso" <?= $estado === 'en curso' ? 'selected' : '' ?>>En curso</option>
                                    <option value="completado" <?= $estado === 'completado' ? 'selected' : '' ?>>Completado</option>
                                    <option value="cancelado" <?= $estado === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                                <select id="cliente_id" class="p-2 border rounded" onchange="filtrar()">
                                    <option value="">Todos los clientes</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= $cliente_id == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="date" id="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="p-2 border rounded" onchange="filtrar()">
                            </div>

                            <?php if (count($proyectos) === 0): ?>
                                <p class="text-gray-600">No se encontraron proyectos con los filtros aplicados.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                                        <thead>
                                            <tr class="bg-gray-100 text-left">
                                                <th class="p-2 border">ID</th>
                                                <th class="p-2 border">Cliente</th>
                                                <th class="p-2 border">Nombre</th>
                                                <th class="p-2 border">Estado</th>
                                                <th class="p-2 border">Progreso</th>
                                                <th class="p-2 border">Tareas</th>
                                                <th class="p-2 border">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($proyectos as $p): ?>
                                                <tr>
                                                    <td class="p-2 border">#<?= $p['id'] ?></td>
                                                    <td class="p-2 border"><?= htmlspecialchars($p['cliente']) ?></td>
                                                    <td class="p-2 border"><?= htmlspecialchars($p['nombre']) ?></td>
                                                    <td class="p-2 border">
                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="proyecto_id" value="<?= $p['id'] ?>">
                                                            <select name="nuevo_estado" onchange="this.form.submit()" class="bg-transparent">
                                                                <option value="planificado" <?= $p['estado'] === 'planificado' ? 'selected' : '' ?>>Planificado</option>
                                                                <option value="en curso" <?= $p['estado'] === 'en curso' ? 'selected' : '' ?>>En curso</option>
                                                                <option value="completado" <?= $p['estado'] === 'completado' ? 'selected' : '' ?>>Completado</option>
                                                                <option value="cancelado" <?= $p['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td class="p-2 border">
                                                        <?php
                                                        $total = $progreso[$p['id']]['total'] ?? 0;
                                                        $completadas = $progreso[$p['id']]['completadas'] ?? 0;
                                                        echo "$completadas / $total";
                                                        ?>
                                                    </td>
                                                    <td class="p-2 border">
                                                        <button onclick="toggleTareas(<?= $p['id'] ?>)" class="text-green-700 hover:underline">Ver tareas</button>
                                                    </td>
                                                    <td class="p-2 border">
                                                        <a href="ver-proyecto.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:underline">Ver</a> |
                                                        <a href="editar-proyecto.php?id=<?= $p['id'] ?>" class="text-orange-600 hover:underline">Editar</a> |
                                                        <a href="eliminar-proyecto.php?id=<?= $p['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar proyecto?')">Eliminar</a>
                                                    </td>
                                                </tr>
                                                <tr id="tareas-<?= $p['id'] ?>" class="hidden">
                                                    <td colspan="7" class="p-2 border bg-gray-50 text-sm text-black">
                                                        <?php if (!empty($tareas_proyecto[$p['id']])): ?>
                                                            <ul class="list-disc pl-6">
                                                                <?php foreach ($tareas_proyecto[$p['id']] as $t): ?>
                                                                    <li class="<?= $t['estado'] === 'finalizada' ? 'text-green-600' : 'text-black' ?>">
                                                                        <?= htmlspecialchars($t['titulo']) ?> - <?= ucfirst($t['estado']) ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p class="text-gray-600 italic">No hay tareas para este proyecto.</p>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </main>



                </div>
            </div>
            <!-- footer -->
            <div class="block print:hidden border-t pt-8 pb-12 dark:border-slate-700/40">
                <div class="container mx-auto px-4">
                    <!-- Footer Start -->
                    <footer class="footer bg-transparent text-center font-medium text-slate-600 dark:text-slate-400 md:text-left">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        Robotech
                        <span class="float-right hidden text-slate-600 dark:text-slate-400 md:inline-block">
                            Crafted with <i class="ti ti-heart text-red-500"></i> by Mannatthemes
                        </span>
                    </footer>
                    <!-- end Footer -->
                </div>
            </div>

        </div><!--end container-->
    </div>
</body>

</html>

<script src="assets/libs/lucide/umd/lucide.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="assets/js/pages/analytics-index.init.js"></script>
<script src="assets/js/app.js"></script>


</body>

</html>

