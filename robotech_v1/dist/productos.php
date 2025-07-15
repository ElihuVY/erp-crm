<?php

include 'conexion.php';

$organizacion_id = $_SESSION['organizacion_id'];

$stmt = $conn->prepare("SELECT * FROM productos WHERE organizacion_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $organizacion_id);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);


// Get organizations linked to current user
$user_id = $_SESSION['usuario_id'];
$org_query = $conn->prepare("SELECT o.id, o.nombre 
                           FROM organizaciones o 
                           INNER JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
                           WHERE uo.usuario_id = ?");
$org_query->bind_param("i", $user_id);
$org_query->execute();
$org_result = $org_query->get_result();

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
    <title>Gestión de Productos</title>
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

    <!-- leftbar-tab-menu -->

    <?php
    include 'menu.php';
    ?>



    <div class="xl:w-full">
        <main class="pt-[90px] px-4 pb-16 lg:ms-[260px]">

        <div class="max-w-6xl mx-auto bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm dark:shadow-slate-700/10">
                

                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-200">📦 Gestión de Productos</h1>
                    <a href="crear-producto.php"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        ➕ Crear Producto
                    </a>
                </div>

                <?php if (count($productos) === 0): ?>
                    <p class="text-gray-600 dark:text-slate-400">No hay productos registrados para esta organización.</p>
                <?php else: ?>

                    <!-- Buscador y filtros -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <input type="text" id="buscador" placeholder="🔍 Buscar por nombre o descripción..."
                            class="border border-gray-300 dark:border-slate-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-slate-200 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                        <select id="filtroTipo"
                            class="border border-gray-300 dark:border-slate-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-slate-200 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">Todos los tipos</option>
                            <option value="producto">Producto</option>
                            <option value="servicio">Servicio</option>
                            <option value="contenido_digital">Contenido Digital</option>
                        </select>


                        <select id="filtroOrganizacion"
                            class="border border-gray-300 dark:border-slate-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-slate-200 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">Todas las organizaciones</option>
                            <?php while ($org = $org_result->fetch_assoc()): ?>
                                <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-slate-700">
                        <table class="w-full table-auto text-sm text-left text-gray-700 dark:text-slate-300">
                            <thead class="bg-gray-100 dark:bg-slate-800 font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border dark:border-slate-700">ID</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Nombre</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Descripción</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Tipo</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Organización</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Precio (€)</th>
                                    <th class="px-4 py-3 border dark:border-slate-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get user's linked organizations
                                $user_id = $_SESSION['usuario_id'];
                                $org_stmt = $conn->prepare("SELECT DISTINCT o.* FROM organizaciones o 
                                    INNER JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
                                    WHERE uo.usuario_id = ?");
                                $org_stmt->bind_param("i", $user_id);
                                $org_stmt->execute();
                                $org_result = $org_stmt->get_result();
                                $org_ids = [];
                                while ($org = $org_result->fetch_assoc()) {
                                    $org_ids[] = $org['id'];
                                }

                                // Get products from all linked organizations
                                if (!empty($org_ids)) {
                                    $org_ids_str = implode(',', $org_ids);
                                    $productos = $conn->query("SELECT p.*, o.nombre as org_nombre 
                                        FROM productos p 
                                        INNER JOIN organizaciones o ON p.organizacion_id = o.id 
                                        WHERE p.organizacion_id IN ($org_ids_str) 
                                        ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);

                                    foreach ($productos as $producto):
                                ?>
                                        <tr
                                            data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                            data-descripcion="<?= htmlspecialchars($producto['descripcion']) ?>"
                                            data-tipo="<?= $producto['tipo'] ?>"
                                            class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                            <td class="px-4 py-2 border dark:border-slate-700"><?= $producto['id'] ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700"><?= htmlspecialchars($producto['nombre']) ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700"><?= htmlspecialchars($producto['descripcion']) ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700"><?= ucfirst($producto['tipo']) ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700"><?= htmlspecialchars($producto['org_nombre']) ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700">€<?= number_format($producto['precio'], 2) ?></td>
                                            <td class="px-4 py-2 border dark:border-slate-700 space-x-2">
                                                <a href="editar-producto.php?id=<?= $producto['id'] ?>" class="text-blue-600 hover:underline">✏️ Editar</a>
                                                <a href="eliminar-producto.php?id=<?= $producto['id'] ?>" class="text-red-600 hover:underline"
                                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">🗑️ Eliminar</a>
                                            </td>
                                        </tr>
                                <?php
                                    endforeach;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Back link -->
                <div class="mt-6">
                    <a href="index.php" class="text-blue-600 hover:underline">⬅ Volver al panel</a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="print:hidden border-t pt-8 pb-12 dark:border-slate-700/40 text-center md:text-left text-sm font-medium text-slate-600 dark:text-slate-400">
            <div class="container mx-auto px-4">
                &copy; <script>
                    document.write(new Date().getFullYear());
                </script> Robotech
                <span class="float-right hidden md:inline-block">
                    Crafted with <i class="ti ti-heart text-red-500"></i> by Mannatthemes
                </span>
            </div>
        </footer>
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
        document.addEventListener("DOMContentLoaded", () => {
            const buscador = document.getElementById("buscador");
            const filtroTipo = document.getElementById("filtroTipo");
            const filtroOrganizacion = document.getElementById("filtroOrganizacion");

            function filtrarTabla() {
                const texto = buscador.value.toLowerCase();
                const tipo = filtroTipo.value;
                const organizacion = filtroOrganizacion.value;

                document.querySelectorAll("tbody tr").forEach(row => {
                    const nombre = row.dataset.nombre.toLowerCase();
                    const descripcion = row.dataset.descripcion.toLowerCase();
                    const tipoRow = row.dataset.tipo;

                    // Get organization ID from the organization name cell (4th column)
                    const orgCell = row.cells[3];
                    const orgNombre = orgCell ? orgCell.textContent : '';

                    const coincideTexto = nombre.includes(texto) || descripcion.includes(texto);
                    const coincideTipo = tipo === "" || tipoRow === tipo;

                    // Check if organization matches selected filter
                    let coincideOrganizacion = true;
                    if (organizacion !== "") {
                        // Get the organization name from the dropdown for the selected value
                        const selectedOption = filtroOrganizacion.options[filtroOrganizacion.selectedIndex];
                        const selectedOrgName = selectedOption.text;
                        coincideOrganizacion = orgNombre === selectedOrgName;
                    }

                    row.style.display = (coincideTexto && coincideTipo && coincideOrganizacion) ? "" : "none";
                });
            }

            buscador.addEventListener("input", filtrarTabla);
            filtroTipo.addEventListener("change", filtrarTabla);
            filtroOrganizacion.addEventListener("change", filtrarTabla);
        });
    </script>

</body>

</html>
<?php
