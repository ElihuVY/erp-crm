<?php
include 'conexion.php';

$cliente_editar = null;

// Crear cliente
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $direccion = $_POST['direccion'];
    $notas = $_POST['notas'];

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, empresa, direccion, notas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $email, $telefono, $empresa, $direccion, $notas);
    $stmt->execute();
    header("Location: clientes.php");
    exit();
}

// Actualizar cliente
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $direccion = $_POST['direccion'];
    $notas = $_POST['notas'];

    $stmt = $conn->prepare("UPDATE clientes SET nombre=?, email=?, telefono=?, empresa=?, direccion=?, notas=? WHERE id=?");
    $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $empresa, $direccion, $notas, $id);
    $stmt->execute();
    header("Location: clientes.php");
    exit();
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $presupuestos = $conn->query("SELECT COUNT(*) FROM presupuestos WHERE cliente_id = $id")->fetch_row()[0];
    $facturas = $conn->query("SELECT COUNT(*) FROM facturas WHERE cliente_id = $id")->fetch_row()[0];

    if ($presupuestos > 0 || $facturas > 0) {
        echo "<script>alert('❌ No se puede eliminar el cliente porque tiene presupuestos o facturas asociadas.'); window.location.href='clientes.php';</script>";
    } else {
        $conn->query("DELETE FROM clientes WHERE id = $id");
        header("Location: clientes.php");
        exit();
    }
}

// Marcar o desmarcar favorito
if (isset($_GET['favorito'])) {
    $id = intval($_GET['favorito']);
    $res = $conn->query("SELECT favorito FROM clientes WHERE id = $id");
    $row = $res->fetch_assoc();
    $nuevo_estado = $row['favorito'] ? 0 : 1;

    $stmt = $conn->prepare("UPDATE clientes SET favorito=? WHERE id=?");
    $stmt->bind_param("ii", $nuevo_estado, $id);
    $stmt->execute();
    header("Location: clientes.php");
    exit();
}

// Editar cliente
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $res = $conn->query("SELECT * FROM clientes WHERE id = $id");
    $cliente_editar = $res->fetch_assoc();
}

$buscar = $_GET['buscar'] ?? '';
$filtro_empresa = $_GET['empresa'] ?? '';
$solo_favoritos = $_GET['solo_favoritos'] ?? '';
$orden = $_GET['orden'] ?? '';

$condiciones = [];
if (!empty($buscar)) {
    $like = "%" . $conn->real_escape_string($buscar) . "%";
    $condiciones[] = "(nombre LIKE '$like' OR email LIKE '$like' OR telefono LIKE '$like')";
}
if (!empty($filtro_empresa)) {
    $empresa_like = "%" . $conn->real_escape_string($filtro_empresa) . "%";
    $condiciones[] = "empresa LIKE '$empresa_like'";
}
if (!empty($solo_favoritos)) {
    $condiciones[] = "favorito = 1";
}
$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

if ($orden === 'asc') {
    $order_clause = 'ORDER BY nombre ASC';
    $nuevo_orden = 'desc';
} elseif ($orden === 'desc') {
    $order_clause = 'ORDER BY nombre DESC';
    $nuevo_orden = '';
} else {
    $order_clause = 'ORDER BY id DESC';
    $nuevo_orden = 'asc';
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$resultado = $conn->query("SELECT * FROM clientes $where $order_clause");

ob_start();
?>
<tbody>
    <?php while ($cliente = $resultado->fetch_assoc()): ?>
        <tr class="border-b">
            <td class="py-2"><?php echo $cliente['identificador_cliente']; ?></td>
            <td class="py-2"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
            <td class="py-2"><?php echo htmlspecialchars($cliente['email']); ?></td>
            <td class="py-2"><?php echo htmlspecialchars($cliente['telefono']); ?></td>
            <td class="py-2 ">
                <?php echo htmlspecialchars($cliente['empresa']); ?>
                <?php if ($cliente['favorito']): ?><span title="Cliente prioritario">❤️</span><?php endif; ?>
            </td>
            <td class="py-2 flex gap-3 justify-center">
                <a href="detalles-cliente.php?id=<?php echo $cliente['id']; ?>" class="text-indigo-600 hover:underline">Ver ficha</a>
                <a href="editar-cliente.php?id=<?php echo $cliente['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                <a href="clientes.php?eliminar=<?php echo $cliente['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar cliente?')">Eliminar</a>
                <a href="clientes.php?favorito=<?php echo $cliente['id']; ?>" class="text-yellow-500 hover:underline">
                    <?php echo $cliente['favorito'] ? '💔 Quitar' : '❤️ Priorizar'; ?>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
<?php
$tabla_html = ob_get_clean();

if ($isAjax) {
    echo $tabla_html;
    exit;
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
                <div class="flex flex-wrap">
                    <div class="flex items-center py-4 w-full">
                        <div class="w-full">
                            <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
                                <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
                                    <div class="flex justify-between items-center mb-6">
                                        <h1 class="text-2xl font-bold">👥 Clientes</h1>
                                        <a href="http://localhost/erp-crm/robotech_v1/dist/index.php" class="bg-gray-300 hover:bg-gray-400 text-black py-2 px-4 rounded">Salir</a>
                                    </div>

                                    <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-5 gap-4">
                                        <input type="text" name="buscar" placeholder="Buscar por nombre, email o teléfono..." class="p-3 border-2 border-blue-500 rounded w-full font-semibold text-blue-700">
                                        <input type="text" name="empresa" placeholder="Filtrar por empresa..." class="p-3 border-2 border-green-500 rounded w-full font-semibold text-green-700">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="solo_favoritos" value="1">
                                            <span class="text-sm font-medium text-gray-700">Solo prioritarios ❤️</span>
                                        </label>

                                       

                                    </form>
                                    <a href="crear-cliente.php" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">
                                     ➕ Crear Cliente</a>








                                    <?php if ($cliente_editar): ?>
                                        <form method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                                            <input type="hidden" name="id" value="<?php echo $cliente_editar['id']; ?>">

                                            <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" value="<?php echo htmlspecialchars($cliente_editar['nombre']); ?>">
                                            <input type="email" name="email" placeholder="Email" class="p-2 border rounded" value="<?php echo htmlspecialchars($cliente_editar['email']); ?>">
                                            <input type="text" name="telefono" placeholder="Teléfono" class="p-2 border rounded" value="<?php echo htmlspecialchars($cliente_editar['telefono']); ?>">
                                            <input type="text" name="empresa" placeholder="Empresa" class="p-2 border rounded" value="<?php echo htmlspecialchars($cliente_editar['empresa']); ?>">
                                            <input type="text" name="direccion" placeholder="Dirección" class="p-2 border rounded" value="<?php echo htmlspecialchars($cliente_editar['direccion']); ?>">
                                            <textarea name="notas" placeholder="Notas" class="p-2 border rounded sm:col-span-2"><?php echo htmlspecialchars($cliente_editar['notas']); ?></textarea>

                                            <div class="sm:col-span-2 flex gap-4">
                                                <button type="submit" name="actualizar" class="bg-blue-600 text-white py-2 px-4 rounded">
                                                    📂 Guardar Cambios
                                                </button>
                                                <a href="clientes.php" class="py-2 px-4 rounded border">Cancelar</a>
                                            </div>
                                        </form>
                                    <?php endif; ?>




                                    <div class="overflow-x-auto">

                                    
                                    <table class="w-full text-left border-t">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="py-2">Identificador</th>
                                                <th class="py-2">Nombre</th>
                                                <th class="py-2">Email</th>
                                                <th class="py-2">Teléfono</th>
                                                <th class="py-2">Empresa</th>
                                                <th class="py-2 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <?php echo $tabla_html; ?>
                                    </table>
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
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                fetch("get-total-clientes.php")
                    .then(response => response.json())
                    .then(data => {
                        if (data.total_clientes !== undefined) {
                            document.getElementById("total-clientes").textContent = data.total_clientes;
                        } else {
                            console.error("Respuesta inesperada:", data);
                        }
                    })
                    .catch(error => {
                        console.error("Error al obtener total de clientes:", error);
                    });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Total Clientes
                fetch("get-total-clientes.php")
                    .then(res => res.json())
                    .then(data => {
                        if (data.total_clientes !== undefined) {
                            document.getElementById("total-clientes").textContent = data.total_clientes;
                        }
                    })
                    .catch(err => console.error("Error clientes:", err));

                // Total Admins
                fetch("get-total-admins.php")
                    .then(res => res.json())
                    .then(data => {
                        if (data.total_admins !== undefined) {
                            document.getElementById("total-admins").textContent = data.total_admins;
                        }
                    })
                    .catch(err => console.error("Error admins:", err));
            });
        </script>

</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.querySelector('input[name="buscar"]');
        const empresaInput = document.querySelector('input[name="empresa"]');
        const favoritosCheck = document.querySelector('input[name="solo_favoritos"]');
        const tabla = document.querySelector('table');

        function buscarClientes() {
            const buscar = buscarInput.value;
            const empresa = empresaInput.value;
            const soloFavoritos = favoritosCheck.checked ? 1 : 0;

            const params = new URLSearchParams({
                buscar,
                empresa,
                solo_favoritos: soloFavoritos
            });

            fetch(`clientes.php?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const nuevaTabla = document.createElement('tbody');
                    nuevaTabla.innerHTML = html;
                    const viejoTbody = tabla.querySelector('tbody');
                    tabla.replaceChild(nuevaTabla, viejoTbody);
                });
        }

        buscarInput.addEventListener('input', buscarClientes);
        empresaInput.addEventListener('input', buscarClientes);
        favoritosCheck.addEventListener('change', buscarClientes);
    });
</script>
<?php
