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

$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$proyecto_id) {
    $proyectos = $conn->query("SELECT id, nombre FROM proyectos ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Seleccionar Proyecto</title>
        <link rel="stylesheet" href="assets/css/tailwind.min.css">
    </head>
    <body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Selecciona un proyecto</h2>
        <form method="GET" class="space-y-4">
            <select name="proyecto_id" required class="border border-gray-300 p-2 w-full rounded">
                <option value="">Seleccionar proyecto...</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= $p['id'] ?>">#<?= $p['id'] ?> - <?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Continuar</button>
        </form>
    </div>
    </body>
    </html>
    <?php exit(); }

$clientes = $conn->query("SELECT c.id, c.nombre FROM clientes c JOIN proyectos p ON p.cliente_id = c.id WHERE p.id = $proyecto_id LIMIT 1")->fetch_all(MYSQLI_ASSOC);

$hoy = date('Y-m-d');

$tareas_raw = $conn->query("SELECT * FROM tareas WHERE proyecto_id = $proyecto_id ORDER BY fecha_inicio ASC")->fetch_all(MYSQLI_ASSOC);
$tareas = [];
foreach ($tareas_raw as $t) {
    if ($t['tarea_padre_id']) {
        $tareas[$t['tarea_padre_id']]['subtareas'][] = $t;
    } else {
        $t['subtareas'] = [];
        $tareas[$t['id']] = $t;
    }
}

function estadoColor($estado) {
    return match($estado) {
        'pendiente' => 'text-yellow-600',
        'en curso' => 'text-blue-600',
        'finalizada' => 'text-green-600',
        default => 'text-gray-600'
    };
}

function mostrarTareaTarjeta($t, $esSubtarea = false) {
    global $proyecto_id, $clientes, $hoy;
    $margen = $esSubtarea ? 'ml-6 border-l-4 border-blue-200 pl-4' : '';
    $retrasada = ($t['estado'] !== 'finalizada') && (!empty($t['fecha_limite']) && $t['fecha_limite'] < $hoy);
    $icon = match($t['prioridad']) {
        'alta' => '🔥',
        'media' => '⚠️',
        'baja' => '💤',
        default => '📌'
    };
    ?>
    <div class="bg-white shadow border rounded-lg p-4 mb-4 <?= $margen ?>">
        <form method="POST" class="space-y-2">
            <input type="hidden" name="editar" value="1">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-lg"><?= $icon ?></span>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($t['titulo']) ?>" class="font-semibold text-lg w-full border-b border-gray-300 pb-1">
                </div>
                <span class="ml-4 px-2 py-1 text-sm rounded <?= estadoColor($t['estado']) ?>"><?= ucfirst($t['estado']) ?></span>
            </div>
            <div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-500">Responsable</label>
        <select name="responsable" class="w-full border p-1 rounded">
            <option value="">---</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= htmlspecialchars($c['nombre']) ?>" <?= $t['responsable'] === $c['nombre'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="block text-sm text-gray-500">Estado</label>
        <select name="estado" class="w-full border p-1 rounded">
            <option value="pendiente" <?= $t['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="en curso" <?= $t['estado'] === 'en curso' ? 'selected' : '' ?>>En curso</option>
            <option value="finalizada" <?= $t['estado'] === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
        </select>
    
                        <option value="">---</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= htmlspecialchars($c['nombre']) ?>" <?= $t['responsable'] === $c['nombre'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-500">Prioridad</label>
                    <select name="prioridad" class="w-full border p-1 rounded">
                        <option value="">---</option>
                        <option value="baja" <?= $t['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
                        <option value="media" <?= $t['prioridad'] === 'media' ? 'selected' : '' ?>>Media</option>
                        <option value="alta" <?= $t['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-500">Comentario</label>
                <input type="text" name="comentario" value="<?= htmlspecialchars($t['comentario']) ?>" class="w-full border p-1 rounded">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-500">Inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= $t['fecha_inicio'] ?>" class="w-full border p-1 rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-500">Fin</label>
                    <input type="date" name="fecha_limite" value="<?= $t['fecha_limite'] ?>" class="w-full border p-1 rounded">
                </div>
            </div>
            <div class="flex justify-between items-center mt-2">
                <div class="flex gap-3">
                    <button type="submit" class="text-blue-600 hover:underline">Guardar</button>
                    <?php if ($t['estado'] !== 'finalizada'): ?>
                        <a href="?proyecto_id=<?= $proyecto_id ?>&eliminar=<?= $t['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</a>
                    <?php else: ?>
                        <span class="text-gray-400">Finalizada</span>
                    <?php endif; ?>
                </div>
                <a href="?proyecto_id=<?= $proyecto_id ?>&tarea_padre_id=<?= $t['id'] ?>#crear-tarea" class="text-sm text-indigo-600 hover:underline">+ Subtarea</a>
            </div>
            <?php if ($retrasada): ?>
                <p class="mt-2 text-sm text-red-600 italic">⚠ Retraso - fecha límite: <?= date("d/m/Y", strtotime($t['fecha_limite'])) ?></p>
            <?php endif; ?>
        </form>
    </div>
    <?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? null;
    $estado = $_POST['estado'];
    $responsable = $_POST['responsable'] ?? null;
    $prioridad = $_POST['prioridad'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_limite = $_POST['fecha_limite'] ?: null;
    $tarea_padre_id = $_POST['tarea_padre_id'] ?: null;

    $stmt = $conn->prepare("INSERT INTO tareas (proyecto_id, titulo, descripcion, estado, responsable, prioridad, comentario, fecha_inicio, fecha_limite, tarea_padre_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssssssi", $proyecto_id, $titulo, $descripcion, $estado, $responsable, $prioridad, $comentario, $fecha_inicio, $fecha_limite, $tarea_padre_id);
    $stmt->execute();
    header("Location: tareas.php?proyecto_id=$proyecto_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? null;
    $estado_nuevo = $_POST['estado'];
    $responsable = $_POST['responsable'] ?? null;
    $prioridad = $_POST['prioridad'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_limite = $_POST['fecha_limite'] ?: null;

    $stmt = $conn->prepare("UPDATE tareas SET titulo=?, descripcion=?, estado=?, responsable=?, prioridad=?, comentario=?, fecha_inicio=?, fecha_limite=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $titulo, $descripcion, $estado_nuevo, $responsable, $prioridad, $comentario, $fecha_inicio, $fecha_limite, $id);
    $stmt->execute();
    header("Location: tareas.php?proyecto_id=$proyecto_id");
    exit();
}

if (isset($_GET['forzar_cambio_estado'])) {
    $id = $_GET['forzar_cambio_estado'];
    $nuevo_estado = $_GET['nuevo_estado'];
    $conn->query("UPDATE tareas SET estado = '$nuevo_estado' WHERE id = $id");
    header("Location: tareas.php?proyecto_id=$proyecto_id");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $estado = $conn->query("SELECT estado FROM tareas WHERE id = $id")->fetch_assoc()['estado'] ?? null;
    if ($estado !== 'finalizada') {
        $conn->query("DELETE FROM tareas WHERE id = $id");
    }
    header("Location: tareas.php?proyecto_id=$proyecto_id");
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
    </style>
        <meta charset="utf-8" />
        <title>Robotech - Admin & Dashboard Template</title>
        <meta  name="viewport"  content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <meta  content="Tailwind Multipurpose Admin & Dashboard Template"  name="description"/>
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
    
    <body data-layout-mode="light"  data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">
    
        <!-- leftbar-tab-menu -->
        

        <div class="min-h-full z-[99]  fixed inset-y-0 print:hidden bg-gradient-to-t from-[#6f3dc3] from-10% via-[#603dc3] via-40% to-[#5c3dc3] to-100% dark:bg-[#603dc3] main-sidebar duration-300 group-data-[sidebar=dark]:bg-[#603dc3] group-data-[sidebar=brand]:bg-brand group-[.dark]:group-data-[sidebar=brand]:bg-[#603dc3]">
            <div class=" text-center border-b bg-[#603dc3] border-r h-[64px] flex justify-center items-center brand-logo dark:bg-[#603dc3] dark:border-slate-700/40 group-data-[sidebar=dark]:bg-[#603dc3] group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:bg-brand group-[.dark]:group-data-[sidebar=brand]:bg-[#603dc3] group-data-[sidebar=brand]:border-slate-700/40">
                <a href="index.html" class="logo">
                    <span>
                        <img src="assets/images/logo-sm.png" alt="logo-small" class="logo-sm h-8 align-middle inline-block">
                    </span>
                    <span>
                        <img src="assets/images/logo.png" alt="logo-large" class="logo-lg h-[28px] logo-light hidden dark:inline-block ms-1 group-data-[sidebar=dark]:inline-block group-data-[sidebar=brand]:inline-block">
                        <img src="assets/images/logo.png" alt="logo-large"
                            class="logo-lg h-[28px] logo-dark inline-block dark:hidden ms-1 group-data-[sidebar=dark]:hidden group-data-[sidebar=brand]:hidden">
                    </span>
                </a>
            </div>
            <div class="border-r pb-14 h-[100vh] dark:bg-[#603dc3] dark:border-slate-700/40 group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:border-slate-700/40" data-simplebar>
                <div class="p-4 block">
                    <ul class="navbar-nav">
                        <li class="uppercase text-[11px]  text-primary-500 dark:text-primary-400 mt-0 leading-4 mb-2 group-data-[sidebar=dark]:text-primary-400 group-data-[sidebar=brand]:text-primary-300">
                           <span class="text-[9px] text-slate-600 dark:text-slate-500 group-data-[sidebar=dark]:text-slate-500 group-data-[sidebar=brand]:text-slate-400">DashboardS & Apps</span></li>
                        <li>
                            <div id="parent-accordion" data-fc-type="accordion">
                                <a href="#"
                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                   data-fc-type="collapse" data-fc-parent="parent-accordion">
                                    <span data-lucide="home"
                                          class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>Admin</span>
                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180 "></i>
                                </a>

                                <div id="Admin-flush" class="hidden overflow-hidden">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                        <li class="nav-item relative block">
                                            <a href="clientes.php"
                                               class="nav-link hover:text-primary-500 rounded-md dark:hover:text-primary-500 relative flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Clientes
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="facturas.php"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Facturas
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="proyectos.php"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Proyectos
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="productos.php"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Productos
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="admin-customers-details.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Customers Details
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="admin-orders.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Orders
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="admin-order-details.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Order Details
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="admin-refund.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Refund
                                            </a>
                                        </li>
                                    </ul>                            
                                </div>
                                <a href="#"
                                    class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                    data-fc-type="collapse" data-fc-parent="parent-accordion">
                                    <span data-lucide="home"
                                        class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>Customer</span>
                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180 "></i>
                                </a>
                                <div id="Customer-flush" class="hidden  overflow-hidden">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                        <li class="nav-item relative block">
                                            <a href="customers-home.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400 "></i>
                                                Home 
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-pro-details.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Product details
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-products.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Product filter
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-cart.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Cart
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-checkout.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Checkout
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-profile.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Profile
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-stores.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Favourite store
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-wishlist.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Wishlist
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-order-track.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Order track
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="customers-invoice.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                               Invoice
                                            </a>
                                        </li>
                                    </ul>                            
                                </div>
                                <div data-fc-type="collapse" data-fc-parent="parent-accordion">
                                    <a href="#"
                                       class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200">
                                        <span data-lucide="grid"
                                              class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                        <span>Apps</span>
                                        <i class="icofont-thin-down fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                    </a>
                                </div>
                                <div class="hidden  overflow-hidden">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2" id="apps-accordion"
                                        data-fc-type="accordion">
                                        <li class="nav-item relative block">
                                            <a href="apps-chat.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200  flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Chat
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="apps-contact-list.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Contact List
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="apps-calendar.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Calendar
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="apps-files.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                File Mamager
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="apps-invoice.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Invoice
                                            </a>
                                        </li>
                                        <li>
                                            <div id="Email" data-fc-type="collapse" data-fc-parent="apps-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                                   aria-expanded="false" aria-controls="Email-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Email</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400  fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Email-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Email">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="apps-email-inbox.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Inbox
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="apps-email-read.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Read Email
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                
                                    </ul>
                                </div>

                        
                                <div class="border-b border-dashed dark:border-slate-700/40 my-3 group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:border-slate-700/40"></div>
                                <div class="text-[9px] text-slate-600 dark:text-slate-500 group-data-[sidebar=dark]:text-slate-500 group-data-[sidebar=brand]:text-slate-400">
                                    C<span>omponents & Extra</span>
                                </div>
                                <div data-fc-type="collapse" data-fc-parent="parent-accordion">
                                    <a href="#"
                                       class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200">
                                        <span data-lucide="box"
                                              class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                        <span>UI Kit</span>
                                        <i class="icofont-thin-down fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                    </a>
                                </div>
                                <div class="hidden  overflow-hidden">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2" id="UI_Kit-accordion"
                                        data-fc-type="accordion">
                                        <li>
                                            <div id="UI_Elements" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="UI_Elements-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>UI Elements</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="UI_Elements-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="UI_Elements">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="ui-alerts.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Alerts
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-avatars.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Avatars
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-buttons.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Buttons
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-badges.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Budges
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-cards.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Cards
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-carousels.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Carousels
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-dropdowns.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Dropdowns
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-grids.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Grids
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-images.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Images
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-lists.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Lists
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-modals.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Modals
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-navs.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Navs
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-navbars.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Navbars
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-paginations.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Paginations
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-popover-tooltips.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Popover & Tooltips
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-progress.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Progress
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-spinners.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Spinners
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-tabs-accordions.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Tabs & Accordions
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-typography.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Typography
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="ui-videos.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Videos
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li>
                                            <div id="Advanced_UI" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                                   aria-expanded="false" aria-controls="Advanced_UI-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Advanced UI</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400  fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Advanced_UI-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Advanced_UI">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-animation.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Animation
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-clipboard.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Clipboard
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-dragula.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Dragula
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-highlight.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Highlight
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-rangeslider.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Range Slider
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-ratings.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Ratings
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-ribbons.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Ribbons
                                                        </a>
                                                    </li>
                                                    <li class="nav-item relative block">
                                                        <a href="advanced-sweetalerts.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Sweet Alert
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li>
                                            <div id="Forms" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Forms-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Forms</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Forms-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Forms">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="forms-elements.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Basic Elements
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="forms-advance.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Advanced Elements
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="forms-validation.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Validation
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="forms-wizard.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Wizard
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="forms-editors.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Editors
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="forms-uploads.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Uploads
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="forms-img-crop.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Image Crop
                                                        </a>
                                                    </li>                                               
                                                </ul>
                                            </div>
                                        </li>

                                        <li>
                                            <div id="Charts" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Charts-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Charts</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Charts-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Charts">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="charts-apex.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Apex
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="charts-echarts.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Echarts
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="charts-justgage.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            JustGage
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="charts-chartjs.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Chartjs
                                                        </a>
                                                    </li>   
                                                    <li class="nav-item relative block">
                                                        <a href="charts-toast-ui.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            ToastUI
                                                        </a>
                                                    </li>                                                                                         
                                                </ul>
                                            </div>
                                        </li>
                                        <li>
                                            <div id="Tables" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Tables-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Tables</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Tables-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Tables">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="tables-basic.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Basic
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="tables-datatable.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Datatable
                                                        </a>
                                                    </li>                                                                                   
                                                </ul>
                                            </div>
                                        </li>
                                        <li>
                                            <div id="Icons" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Icons-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Icons</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Icons-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Icons">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="icons-lucide.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Lucide
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="icons-fontawesome.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Fontawesome
                                                        </a>
                                                    </li>     
                                                    <li class="nav-item relative block">
                                                        <a href="icons-icofont.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Icofont
                                                        </a>
                                                    </li>                                                                                
                                                </ul>
                                            </div>
                                        </li>
                                        <li>
                                            <div id="Maps" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Maps-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Maps</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Maps-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Maps">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="maps-google.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Google Maps
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="maps-leaflet.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Leaflet Maps
                                                        </a>
                                                    </li>     
                                                    <li class="nav-item relative block">
                                                        <a href="maps-vector.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Vector Maps
                                                        </a>
                                                    </li>                                                                             
                                                </ul>
                                            </div>
                                        </li>
                                        <li>
                                            <div id="Email-Temp" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                                <a href="#"
                                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                   aria-expanded="false" aria-controls="Email-Temp-flush">
                                                    <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                    <span>Email Templates</span>
                                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                                </a>
                                            </div>
                                            <div id="Email-Temp-flush" class=" hidden  overflow-hidden "
                                                 aria-labelledby="Email-Temp">
                                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                    <li class="nav-item relative block">
                                                        <a href="email-templates-alert.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Alert Email
                                                        </a>
                                                    </li> 
                                                    <li class="nav-item relative block">
                                                        <a href="email-templates-basic.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Basic Email
                                                        </a>
                                                    </li>     
                                                    <li class="nav-item relative block">
                                                        <a href="email-templates-billing.html"
                                                           class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                            Billing Email
                                                        </a>
                                                    </li>                                                                             
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <a href="#"
                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                   data-fc-type="collapse" data-fc-parent="parent-accordion">
                                    <span data-lucide="file-plus"
                                          class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>Pages</span>
                                    <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400  fc-collapse-open:rotate-180 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                </a>

                                <div id="Pages-flush" class="hidden  overflow-hidden">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                        <li class="nav-item relative block">
                                            <a href="pages-blogs.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Blogs
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="pages-pricing.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Pricing
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="pages-profile.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Profile
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="pages-starter.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Starter
                                            </a>
                                        </li>  
                                        <li class="nav-item relative block">
                                            <a href="pages-timeline.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Timeline
                                            </a>
                                        </li>                             
                                    </ul>
                                </div>
                                <a href="#"
                                   class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                   data-fc-type="collapse" data-fc-parent="parent-accordion">
                                        <span data-lucide="lock"
                                              class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>Authentication</span>
                                    <i class="icofont-thin-down  fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                </a>
                                <div id="Authentication-flush" class="hidden  overflow-hidden"
                                     aria-labelledby="Authentication">
                                    <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                        <li class="nav-item relative block">
                                            <a href="auth-login.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Log In
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="auth-register.php"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Register
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="auth-recover-pw.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Recover Password
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="auth-lock-screen.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                Lock Screen
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="auth-404.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                404
                                            </a>
                                        </li>
                                        <li class="nav-item relative block">
                                            <a href="auth-500.html"
                                               class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                500
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="rounded-md py-4 px-3 mt-12  mb-4 relative bg-primary-300/10 text-center">
                        <a href="javascript: void(0);" class="float-right close-btn text-slate-400">
                            <i class="mdi mdi-close"></i>
                        </a>
                        <h5 class="my-3 text-lg font-medium text-slate-700 dark:text-slate-300 group-data-[sidebar=dark]:text-slate-300 group-data-[sidebar=brand]:text-slate-300">Mannat Themes</h5>
                        <p class="mb-3 text-sm text-slate-400">We Design and Develop Clean and High Quality Web Applications</p>
                        <button class="px-2 py-1 mb-2 text-orange-400 hover:text-white border border-orange-300 hover:bg-orange-300 focus:outline-none  rounded text-sm  text-center dark:border-orange-300 dark:text-orange-300 dark:hover:text-white dark:hover:bg-orange-300 ">Upgrade your plan</button>
                
                    </div>
                </div>
            </div>
        </div>

        
            <nav id="topbar" class="topbar border-b  dark:border-slate-700/40  fixed inset-x-0  duration-300
             block print:hidden z-50">
            <div class="mx-0 flex max-w-full flex-wrap items-center lg:mx-auto relative top-[50%] start-[50%] transform -translate-x-1/2 -translate-y-1/2">
              <div class="ltr:mx-2  rtl:mx-2">
                <button id="toggle-menu-hide" class="button-menu-mobile flex rounded-full md:me-0 relative">
                  <!-- <i class="ti ti-chevrons-left text-3xl  top-icon"></i> -->
                  <i data-lucide="menu" class="top-icon w-5 h-5"></i>
                </button>
              </div>
              <div class="flex items-center md:w-[40%] lg:w-[30%] xl:w-[20%]">
                <div class="relative ltr:mx-2 rtl:mx-2 self-center">
                  <button class="px-2 py-1 bg-primary-500/10 border border-transparent collapse:bg-green-100 text-primary text-sm rounded hover:bg-blue-600 hover:text-white"><i class="ti ti-plus me-1"></i> New Task</button>
                </div>
              </div>
      
              <div class="order-1 ltr:ms-auto rtl:ms-0 rtl:me-auto flex items-center md:order-2">
                <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                  <button
                    type="button"
                    class="dropdown-toggle flex rounded-full md:me-0"
                    aria-expanded="false"
                     data-fc-autoclose="both" data-fc-type="dropdown">
                    <span data-lucide="search" class="top-icon w-5 h-5"></span>
                  </button>

                  <div
                    class="left-auto right-0 z-50 my-1 hidden min-w-[300px]
                    list-none divide-y  divide-gray-100 rounded-md border-slate-700
                    md:border-white text-base shadow dark:divide-gray-600 bg-white
                    dark:bg-slate-800" onclick="event.stopPropagation()">
                    <div class="relative">
                      <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center
                        pl-3">
                        <i class="ti ti-search text-gray-400 z-10"></i>
                      </div>
                      <input
                        type="text"
                        id="email-adress-icon"
                        class="block w-full rounded-lg border border-slate-200 dark:border-slate-700/60 bg-slate-200/10 p-1.5
                        pl-10 text-slate-600 dark:text-slate-400 outline-none focus:border-slate-300
                        focus:ring-slate-300 dark:bg-slate-800/20 sm:text-sm"
                        placeholder="Search..."
                        />
                    </div>
                  </div>
                </div>
                <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4">

                  <button id="toggle-theme" class="flex rounded-full md:me-0 relative">
                    <span data-lucide="moon" class="top-icon w-5 h-5 light "></span>
                    <span data-lucide="sun" class="top-icon w-5 h-5 dark hidden"></span>
                  </button>
                </div>
                <div class="ltr:me-2 ltr:lg:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                  <button
                    type="button"
                    class="dropdown-toggle flex rounded-full md:me-0"
                    id="Notifications"
                    aria-expanded="false"
                     data-fc-autoclose="both" data-fc-type="dropdown">
                    <span data-lucide="bell" class="top-icon w-5 h-5"></span>
                  </button>

                  <div
                    class="left-auto right-0 z-50 my-1 hidden w-64
                    list-none divide-y h-52 divide-gray-100 rounded border border-slate-700/10
                   text-base shadow dark:divide-gray-600 bg-white
                    dark:bg-slate-800"
                    id="navNotifications" data-simplebar>
                    <ul class="py-1" aria-labelledby="navNotifications">
                      <li class="py-2 px-4">
                        <a href="javascript:void(0);" class="dropdown-item">
                          <div class="flex">
                              <div class="h-8 w-8 rounded-full bg-primary-500/20 inline-flex me-3">
                                <span data-lucide="shopping-cart" class="w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"></span>
                              </div>
                            <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                              <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Karen Robinson</p>
                              <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                Hey ! i'm available here
                              </p>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li class="py-2 px-4">
                        <a href="javascript:void(0);" class="dropdown-item">
                          <div class="flex">
                            <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3"
                              src="assets/images/users/avatar-3.png" alt="logo" />
                            <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                              <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Your order is placed</p>
                              <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                Dummy text of the printing and industry.
                              </p>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li class="py-2 px-4">
                        <a href="javascript:void(0);" class="dropdown-item">
                          <div class="flex">
                            <div class="h-8 w-8 rounded-full bg-primary-500/20 inline-flex me-3">
                              <span data-lucide="user" class="w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"></span>
                            </div>
                            <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                              <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Robert McCray</p>
                              <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                Good Morning!
                              </p>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li class="py-2 px-4">
                        <a href="javascript:void(0);" class="dropdown-item">
                          <div class="flex">
                            <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3"
                              src="assets/images/users/avatar-9.png" alt="logo" />
                            <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                              <p class="text-sm font-medium  text-gray-900 truncate
                                dark:text-gray-300">Meeting with designers</p>
                              <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                It is a long established fact that a reader.
                              </p>
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="me-2  dropdown relative">
                  <button
                    type="button"
                    class="dropdown-toggle flex items-center rounded-full text-sm
                    focus:bg-none focus:ring-0 dark:focus:ring-0 md:me-0"
                    id="user-profile"
                    aria-expanded="false"
                     data-fc-autoclose="both" data-fc-type="dropdown">
                    <img
                      class="h-8 w-8 rounded-full"
                      src="assets/images/users/avatar-1.png"
                      alt="user photo"
                      />
                    <span class="ltr:ms-2 rtl:ms-0 rtl:me-2 hidden text-left xl:block">
                      <span class="block font-medium text-slate-600 dark:text-gray-300">Maria Gibson</span>
                      <span class="-mt-0.5 block text-xs text-slate-500 dark:text-gray-400">Admin</span>
                    </span>
                  </button>

                  <div
                    class="left-auto right-0 z-50 my-1 hidden list-none
                    divide-y divide-gray-100 rounded border border-slate-700/10
                    text-base shadow dark:divide-gray-600 bg-white dark:bg-slate-800 w-40"
                    id="navUserdata">
            
                    <ul class="py-1" aria-labelledby="navUserdata">
                      <li>
                        <a
                          href="#"
                          class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <span data-lucide="user"
                                          class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                          Profile</a>
                      </li>
                      <li>
                        <a
                          href="#"
                          class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <span data-lucide="settings"
                                          class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                          Settings</a>
                      </li>
                      <li>
                        <a
                          href="#"
                          class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <span data-lucide="dollar-sign"
                                          class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                          Earnings</a>
                      </li>
                      <li>
                        <a
                          href="auth-lockscreen.html"
                          class="flex items-center py-2 px-3 text-sm text-red-500 hover:bg-gray-50 hover:text-red-600
                          dark:text-red-500 dark:hover:bg-gray-900/20
                          dark:hover:text-red-500">
                          <span data-lucide="power"
                                          class="w-4 h-4 inline-block text-red-500 dark:text-red-500 me-2"></span>
                          Sign out</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </nav>


        <div class="ltr:flex flex-1 rtl:flex-row-reverse">
        <div class="page-wrapper relative ltr:ml-auto rtl:mr-auto rtl:ml-0 w-[calc(100%-260px)] px-4 pt-[64px] duration-300">
    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center py-4 w-full">
                <div class="w-full">

<main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">📋 Tareas del proyecto</h1>
            <a href="gantt.php?proyecto_id=<?= $proyecto_id ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">📊 Ver en Gantt</a>
        </div>

        <!-- Formulario Crear Nueva Tarea -->
        <div class="bg-gray-50 border border-gray-200 p-4 rounded mb-6">
            <h2 class="text-lg font-semibold mb-4">➕ Nueva tarea</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="crear" value="1">
                <div class="col-span-2">
                    <input type="text" name="titulo" placeholder="Título de la tarea" required class="w-full border p-2 rounded">
                </div>
                <div class="col-span-2">
                    <textarea name="descripcion" placeholder="Descripción..." class="w-full border p-2 rounded"></textarea>
                </div>
                <div class="col-span-2">
                    <textarea name="comentario" placeholder="Comentario interno..." class="w-full border p-2 rounded"></textarea>
                </div>
                <div>
                    <select name="estado" required class="w-full border p-2 rounded">
                        <option value="pendiente">Pendiente</option>
                        <option value="en curso">En curso</option>
                        <option value="finalizada">Finalizada</option>
                    </select>
                </div>
                <div>
                    <select name="responsable" class="w-full border p-2 rounded">
                        <option value="">Responsable</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= htmlspecialchars($c['nombre']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="prioridad" class="w-full border p-2 rounded">
                        <option value="">Prioridad</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="fecha_inicio" class="w-full border p-2 rounded">
                </div>
                <div>
                    <input type="date" name="fecha_limite" class="w-full border p-2 rounded">
                </div>
                <div class="col-span-2">
                    <select name="tarea_padre_id" class="w-full border p-2 rounded">
                        <option value="">Subtarea de...</option>
                        <?php foreach ($tareas as $t): ?>
                            <option value="<?= $t['id'] ?>">#<?= $t['id'] ?> - <?= htmlspecialchars($t['titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-2 text-right">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">💾 Guardar tarea</button>
                </div>
            </form>
        </div>

        <!-- Lista de tareas -->
        <div class="space-y-4">
            <?php foreach ($tareas as $t): ?>
                <?php mostrarTareaTarjeta($t); ?>
                <?php foreach ($t['subtareas'] as $sub): ?>
                    <?php mostrarTareaTarjeta($sub, true); ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-6">
            <a href="index.html" class="text-blue-600 hover:underline">⬅ Volver al panel</a>
        </div>
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
        </div>

        <!-- JAVASCRIPTS -->
        <!-- <div class="menu-overlay"></div> -->
        <script src="assets/libs/lucide/umd/lucide.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
        <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="assets/js/pages/analytics-index.init.js"></script>
        <script src="assets/js/app.js"></script>
        <!-- JAVASCRIPTS -->
        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.5.0/dist/frappe-gantt.min.js"></script>
<script>
    const hoy = "<?= $hoy ?>";
    const proyectoId = <?= $proyecto_id ?>;
    let gantt;

    function cargarTareas() {
        const estado = document.getElementById("filtro-estado").value;
        const responsable = document.getElementById("filtro-responsable").value;
        const url = `cargar-tareas-gantt.php?proyecto_id=${proyectoId}&estado=${estado}&responsable=${responsable}`;

        fetch(url)
            .then(res => res.json())
            .then(tareas => {
                document.getElementById("gantt").innerHTML = "";
                gantt = new Gantt("#gantt", tareas, {
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    on_date_change: function(task, start, end) {
                        const dependencias = tareas.find(t => t.id === task.id)?.dependencies;
                        if (dependencias) {
                            const padre = tareas.find(t => t.id === dependencias);
                            if (padre && new Date(start) < new Date(padre.end)) {
                                alert("❌ Esta tarea depende de otra que aún no ha finalizado.");
                                cargarTareas();
                                return;
                            }
                        }

                        if (!confirm("¿Estás seguro de que quieres aplicar estos cambios?")) return;

                        fetch("actualizar-fechas.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                id: task.id.replace("T", ""),
                                fecha_inicio: start,
                                fecha_fin: end
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.success ? "✅ Fechas actualizadas correctamente." : "❌ Error al actualizar.");
                            cargarTareas();
                        })
                        .catch(() => alert("❌ Error en el servidor."));
                    },
                    custom_popup_html: function(task) {
                        const retraso = (task.estado !== 'finalizada' && task.end < hoy);
                        return `
                            <div class="details-container">
                                <h4>${task.name}</h4>
                                <p>🗓 <strong>Inicio:</strong> ${task.start}</p>
                                <p>⏰ <strong>Fin:</strong> ${task.end}</p>
                                ${task.responsable ? `<p>👤 <strong>Responsable:</strong> ${task.responsable}</p>` : ''}
                                <p><strong>Progreso:</strong> ${task.progress}%</p>
                                <div class="progress-bar"><div class="progress-fill" style="width: ${task.progress}%;"></div></div>
                                ${retraso ? `<p class="alert-retraso">⚠️ Esta tarea va con retraso</p>` : ''}
                            </div>
                        `;
                    }
                });

                setTimeout(() => {
                    document.querySelectorAll('.bar').forEach(bar => {
                        const taskId = bar.getAttribute('data-id');
                        const task = tareas.find(t => t.id === taskId);
                        if (task && task.color) {
                            bar.setAttribute('fill', task.color);
                            const bg = bar.closest('.bar-wrapper')?.querySelector('.bar-background');
                            if (bg) bg.setAttribute('fill', task.color);
                        }
                    });
                }, 300);
            });
    }

    document.getElementById("filtro-estado").addEventListener("change", cargarTareas);
    document.getElementById("filtro-responsable").addEventListener("change", cargarTareas);
    cargarTareas();
</script>
                
    </body>
</html>
<?php
