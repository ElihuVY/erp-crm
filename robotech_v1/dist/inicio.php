<?php
session_start();

include 'conexion.php';




?>



<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
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
                    <div class="">
                        <div class="flex flex-wrap justify-between">
                            <div class="items-center ">
                                <h1 class="font-medium text-3xl block dark:text-slate-100">Ecommerce</h1>
                                <ol class="list-reset flex text-sm">
                                    <li><a href="#" class="text-gray-500 dark:text-slate-400">Robotech</a></li>
                                    <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                    <li class="text-gray-500 dark:text-slate-400">Dashboard</li>
                                    <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                    <li class="text-primary-500 hover:text-primary-600 dark:text-primary-400">Ecommerce</li>
                                </ol>
                            </div>
                            <div class="flex items-center">
                                <div class="today-date leading-5 mt-2 lg:mt-0 form-input w-auto rounded-md border inline-block border-primary-500/60 dark:border-primary-500/60 text-primary-500 bg-transparent px-3 py-1 focus:outline-none focus:ring-0 placeholder:text-slate-400/70 placeholder:font-normal placeholder:text-sm hover:border-primary-400 focus:border-primary-500 dark:focus:border-primary-500  dark:hover:border-slate-700">
                                    <input type="text" class="dash_date border-0 focus:border-0 focus:outline-none" value="<?php echo date('d/m/Y'); ?>" readonly required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end container-->

    <div class="xl:w-full  min-h-[calc(100vh-138px)] relative pb-14">
        <div class="grid grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4">
            <div class="col-span-12 sm:col-span-6 md:col-span-6 lg:col-span-3 xl:col-span-3">
                <div class="bg-white shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900  rounded-md w-full relative mb-4">
                    <div class="flex-auto p-4">
                        <div class="flex justify-between xl:gap-x-2 items-cente">
                            <div class="self-center">
                                <p class="text-gray-800 font-semibold dark:text-slate-400 text-lg uppercase">Total Clientes</p>

                                <h3 id="total-clientes" class="my-4 font-semibold text-[30px] dark:text-slate-200">0</h3>
                            </div>
                            <div class="self-center">
                                <i data-lucide="shopping-cart" class=" h-16 w-16 stroke-primary-500/30"></i>
                            </div>
                        </div>
                        <p class="truncate text-slate-400"><span class="text-green-500"><i class="mdi mdi-trending-up"></i>8.5%</span> New Sessions Today</p>
                    </div><!--end card-body-->
                    <div class="flex-auto p-0 overflow-hidden">
                        <div class="flex mb-0 h-full">
                            <div class="w-full">
                                <div class="apexchart-wrapper">
                                    <div id="apex_column1" class="chart-gutters"></div>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div> <!--end inner-grid-->
            </div><!--end col-->
            <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-3 xl:col-span-3">
                <div class="bg-white shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900  rounded-md w-full relative mb-4">
                    <div class="flex-auto p-4">
                        <div class="flex justify-between xl:gap-x-2 items-cente">
                            <div class="self-center">
                                <p class="text-gray-800 font-semibold dark:text-slate-400 uppercase">Administradores</p>
                                <h3 id="total-admins" class="my-4 font-semibold text-[30px] dark:text-slate-200">0</h3>
                            </div>
                            <div class="self-center">
                                <i data-lucide="users" class=" h-16 w-16 stroke-green/30"></i>
                            </div>
                        </div>
                        <p class="truncate text-slate-400"><span class="text-red-500"><i class="mdi mdi-trending-down"></i>0.6%</span> Bounce Rate Weekly</p>
                    </div>

                    <div class="flex-auto p-0 overflow-hidden">
                        <div class="flex mb-0 h-full">
                            <div class="w-full">
                                <div class="apexchart-wrapper">
                                    <div id="dash_spark_1" class="chart-gutters"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-3 xl:col-span-3">
                <div class="bg-white shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900 rounded-md w-full relative mb-4">
                    <div class="flex-auto p-4">
                        <div class="flex justify-between xl:gap-x-2 items-center">
                            <div class="self-center">
                                <?php
                                // Obtener todas las organizaciones del usuario
                                $usuario_id = $_SESSION['usuario_id'];
                                $org_ids = [];

                                $sql_org = "SELECT organizacion_id FROM usuario_organizacion WHERE usuario_id = $usuario_id";
                                $result_org = mysqli_query($conn, $sql_org);

                                while ($row = mysqli_fetch_assoc($result_org)) {
                                    $org_ids[] = $row['organizacion_id'];
                                }

                                if (!empty($org_ids)) {
                                    $org_ids_str = implode(',', $org_ids);

                                    // Obtener total de facturas pendientes y suma
                                    $sql = "SELECT COUNT(*) as total, SUM(total) as monto_total 
                            FROM facturas 
                            WHERE estado = 'Pendiente' 
                            AND fecha_emision >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                            AND organizacion_id IN ($org_ids_str)";
                                    $result = mysqli_query($conn, $sql);
                                    $row = mysqli_fetch_assoc($result);
                                    $total_pendientes = $row['total'];
                                    $monto_total = $row['monto_total'] ?? 0;
                                } else {
                                    $total_pendientes = 0;
                                    $monto_total = 0;
                                }
                                ?>
                                <p class="text-gray-800 font-semibold dark:text-slate-400 uppercase">Facturas Pendientes</p>
                                <h3 class="my-4 font-semibold text-[30px] dark:text-slate-200"><?php echo $total_pendientes; ?></h3>
                                <p class="text-red-500 font-medium">$<?php echo number_format($monto_total, 2); ?></p>
                            </div>
                            <div class="self-center">
                                <i data-lucide="file-warning" class="h-16 w-16 stroke-red-500/30"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex-auto p-4 bg-slate-50 dark:bg-gray-800">
                        <div class="overflow-y-auto max-h-[150px]">
                            <table class="w-full">
                                <tbody>
                                    <?php
                                    if (!empty($org_ids)) {
                                        $sql = "SELECT f.id, f.fecha_emision, f.total, f.numero_factura, f.serie_id, s.prefijo 
                                FROM facturas f
                                LEFT JOIN series s ON f.serie_id = s.id
                                WHERE f.estado = 'Pendiente' 
                                AND f.fecha_emision >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                                AND f.organizacion_id IN ($org_ids_str)
                                ORDER BY f.fecha_emision DESC 
                                LIMIT 5";
                                        $result = mysqli_query($conn, $sql);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $fecha = date('d/m/Y', strtotime($row['fecha_emision']));
                                            $numero_completo = $row['prefijo'] ? $row['prefijo'] . $row['numero_factura'] : str_pad($row['id'], 6, '0', STR_PAD_LEFT);
                                    ?>
                                            <tr class="border-b border-dashed border-slate-200 dark:border-slate-700">
                                                <td class="py-2 text-sm text-slate-600 dark:text-slate-400">
                                                    #<?php echo $numero_completo; ?>
                                                </td>
                                                <td class="py-2 text-sm text-slate-600 dark:text-slate-400">
                                                    <?php echo $fecha; ?>
                                                </td>
                                                <td class="py-2 text-sm text-slate-600 dark:text-slate-400 text-right">
                                                    $<?php echo number_format($row['total'], 2); ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="facturas.php?estado=pendiente" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                Ver todas las facturas pendientes →
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-3 xl:col-span-3">
                <div class="bg-[#1b1b22] shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900  rounded-md w-full relative mb-4">
                    <div class="flex-auto p-4">
                        <div class="flex justify-between xl:gap-x-2 items-cente">
                            <div class="self-center">
                                <p class="text-gray-300 font-semibold dark:text-slate-400 uppercase">Total Revenue</p>
                                <h3 class="my-4 font-semibold text-[30px] text-slate-200">$85000</h3>
                            </div>
                            <div class="self-center">
                                <i data-lucide="dollar-sign" class=" h-16 w-16 stroke-[#2ecee1]/30"></i>
                            </div>
                        </div>
                        <p class="truncate text-slate-400"><span class="text-green-500"><i class="mdi mdi-trending-up"></i>10.5%</span> Completions Weekly</p>
                    </div><!--end card-body-->
                    <div class="flex-auto p-4 pt-0 -mt-1">
                        <div class="grid grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4">

                            <div class="col-span-12 sm:col-span-6 md:col-span-6 lg:col-span-6 xl:col-span-3">
                                <img src="assets/images/widgets/wallet.png" alt="" class="w-full h-auto">
                            </div>
                            <div class="col-span-12 sm:col-span-6 md:col-span-6 lg:col-span-6 xl:col-span-8 text-end self-center">
                                <button class="px-4 py-1 font-medium text-white transition duration-200 ease-in-out delay-200 skew-y-6 bg-brand-600 border-b-4 border-brand-700 rounded shadow-lg shadow-brand-600/50 hover:skew-y-0 hover:border-brand-700">Withdrawal</button>
                                <!-- <button class="px-2 py-1 bg-brand-500 border border-transparent collapse:bg-green-100 text-white text-sm rounded hover:bg-brand-600 hover:text-white">Withdrawal</button> -->
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div> <!--end inner-grid-->
            </div><!--end col-->
        </div> <!--end grid-->

        <div class="grid grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4">
            <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4">
                <div class="w-full relative mb-4">
                    <div class="flex-auto p-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 sm:col-span-4">
                                <img src="assets/images/widgets/user.png" alt="" class="h-auto w-full">
                            </div><!--end col-->
                            <div class="col-span-12 sm:col-span-8 self-center">
                                <h4 class="font-medium flex-1 self-center mb-2 md:mb-0 dark:text-slate-400 text-xl">Una guía para analizar y optimizar su negocio en línea</h4>
                            </div><!--end col-->
                        </div><!--end grid-->

                        <div class="border-b border-dashed border-slate-300 dark:border-slate-700/40 my-3"></div>
                        <div class="grid grid-cols-12 gap-4 mb-8">
                            <div class="col-span-12 sm:col-span-6">
                                <div id="email_report" class="apex-charts -mb-4"></div>
                            </div><!--end col-->
                            <div class="col-span-12 sm:col-span-6 self-center">
                                <ol id="series_legend" class="list-none list-inside mb-3">
                                    <!-- Aquí se insertan dinámicamente las series -->
                                </ol>

                                <a href="facturas.php" class="inline-block shadow-sm dark:shadow-slate-700/10 focus:outline-none text-slate-600 hover:bg-brand-500 hover:text-white bg-transparent border border-slate-300 dark:bg-transparent dark:text-slate-400 dark:hover:text-white dark:border-gray-700 dark:hover:bg-brand-500  text-sm font-medium py-1 px-3 rounded">Ver detalles <i class="mdi mdi-arrow-right"></i></a>
                            </div><!--end col-->
                        </div><!--end grid-->


                        <h6 class="bg-brand-400/5 shadow-sm dark:shadow-slate-700/10 border border-dashed dark:text-brand-300 border-brand dark:bg-slate-700/40 py-3 px-2 rounded-md  text-center text-brand-500 font-medium mt-3">
                            <i class="ti ti-calendar self-center text-lg mr-1"></i>
                            01 January 2023 to 31 December 2024
                        </h6>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
            <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-8 xl:col-span-8">
                <div class="w-full relative mb-4">
                    <div class="border-b border-dashed border-slate-200 dark:border-slate-700 py-3 px-4 dark:text-slate-300/70">
                        <div class="flex-none md:flex">
                            <h4 class="font-medium flex-1 self-center mb-2 md:mb-0 text-xxl"> Grafica de Facturación</h4>
                            <div class="dropdown inline-block">
                                <button data-fc-autoclose="both" data-fc-type="dropdown" class="dropdown-toggle px-3 py-1 text-xs font-medium text-gray-500 focus:outline-none bg-white rounded border border-gray-200 hover:bg-gray-50 hover:text-slate-800 focus:z-10 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                                    This Month
                                    <i class="fas fa-chevron-down text-xs ml-2"></i>
                                </button>
                                <!-- Dropdown menu -->
                                <div class="right-auto md:right-0 hidden z-10 w-28 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefault">
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white">Hoy</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white">Semana Pasada</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white">Mes Pasado</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white">Este Año</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto p-4">
                        <div id="crm-dash" class="apex-charts mt-5"></div>
                    </div>
                    <!--end card-body-->
                </div> <!--end inner-grid-->
            </div><!--end col-->


        </div> <!--end grid-->
        <div class="grid grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4 mb-4">
            <div class="col-span-12 sm:col-span-12  md:col-span-12 lg:col-span-6 xl:col-span-6 ">
                <div class="bg-white shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900   rounded-md w-full relative">
                    <div class="border-b border-dashed border-slate-200 dark:border-slate-700 py-4 px-4 dark:text-slate-300/70">
                        <h4 class="font-medium flex-1 self-center mb-2 md:mb-0 text-xxl">Últimas Facturas</h4>
                        <p class="text-sm text-slate-400">Ganancias de la Ultima Semana <span class="focus:outline-none text-[11px] bg-brand-500/10 text-brand-500 dark:text-brand-300 rounded font-medium py-[2px] px-2">$18532</span></p>
                    </div><!--end header-title-->
                    <div class="grid grid-cols-1 p-4">
                        <div class="sm:-mx-6 lg:-mx-8">
                            <div class="relative overflow-x-auto block w-full sm:px-6 lg:px-8">
                                <table class="w-full">
                                    <thead class="bg-brand-400/10 dark:bg-slate-700/20">
                                        <tr>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                ID Factura
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Tipo
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Estado
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT id, tipo, estado, total FROM facturas ORDER BY id DESC LIMIT 6";
                                        $result = mysqli_query($conn, $sql);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $estado_class = $row['estado'] == 'Pagada' ? 'text-green-500' : 'text-red-500';
                                        ?>
                                            <tr class="bg-white border-b border-dashed dark:bg-gray-900 dark:border-gray-700/40">
                                                <td class="p-3 text-base text-gray-600 whitespace-nowrap dark:text-gray-400 text-center">
                                                    #<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?>
                                                </td>
                                                <td class="p-3 text-base text-gray-500 whitespace-nowrap dark:text-gray-400 text-center">
                                                    <?php echo $row['tipo']; ?>
                                                </td>
                                                <td class="p-3 text-base text-gray-500 whitespace-nowrap dark:text-gray-400 text-center">
                                                    <span class="<?php echo $estado_class; ?>"><?php echo $row['estado']; ?></span>
                                                </td>
                                                <td class="p-3 text-base text-gray-700 whitespace-nowrap dark:text-gray-400 text-center">
                                                    <span class="font-semibold">$<?php echo number_format($row['total'], 2); ?></span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--end card-body-->




                </div> <!--end card-->
            </div><!--end col-->


            <div class="col-span-12 sm:col-span-12  md:col-span-12 lg:col-span-6 xl:col-span-6 ">
                <div class="bg-white shadow-sm dark:shadow-slate-700/10 dark:bg-gray-900   rounded-md w-full relative">
                    <div class="border-b border-dashed border-slate-200 dark:border-slate-700 py-4 px-4 dark:text-slate-300/70">
                        <h4 class="font-medium flex-1 self-center mb-2 md:mb-0 text-xxl">Productos Más Populares</h4>
                        <p class="text-sm text-slate-400">New Products Last Week <span class="focus:outline-none text-[11px] bg-brand-500/10 text-brand-500 dark:text-brand-300 rounded font-medium py-[2px] px-2">5</span></p>
                    </div><!--end header-title-->

                    <div class="grid grid-cols-1 p-4">
                        <div class="sm:-mx-6 lg:-mx-8">
                            <div class="relative overflow-x-auto block w-full sm:px-6 lg:px-8">
                                <table class="w-full">
                                    <thead class="bg-brand-400/10 dark:bg-slate-700/20">
                                        <tr>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Producto
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Precio
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Total Vendido
                                            </th>
                                            <th scope="col" class="p-3 text-base font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">
                                                Estado
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get products with total sales for user's organization
                                        $sql = "SELECT 
                                            p.nombre,
                                            p.precio as precio_unitario,
                                            SUM(fi.cantidad) as total_vendido
                                           FROM productos p
                                           INNER JOIN factura_items fi ON fi.producto_id = p.id 
                                           INNER JOIN facturas f ON f.id = fi.factura_id
                                           WHERE p.organizacion_id = {$_SESSION['organizacion_id']}
                                           GROUP BY p.id, p.nombre, p.precio
                                           ORDER BY total_vendido DESC
                                           LIMIT 5";

                                        $result = mysqli_query($conn, $sql);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr class="bg-white border-b border-dashed dark:bg-gray-900 dark:border-gray-700/40">
                                                <td class="p-3 text-base font-medium whitespace-nowrap dark:text-white text-center">
                                                    <img src="assets/images/products/default.png" alt="" class="me-2 h-10 inline-block"><?php echo $row['nombre']; ?>
                                                </td>
                                                <td class="p-3 text-base text-gray-500 whitespace-nowrap dark:text-gray-400 text-center">
                                                    $<?php echo number_format($row['precio_unitario'], 2); ?>
                                                </td>
                                                <td class="p-3 text-base text-gray-500 whitespace-nowrap dark:text-gray-400 text-center">
                                                    <?php echo $row['total_vendido']; ?>
                                                </td>
                                                <td class="p-3 text-base text-gray-500 whitespace-nowrap dark:text-gray-400 text-center">
                                                    <span class="focus:outline-none text-[12px] bg-green-600/10 text-green-700 dark:text-green-600 rounded font-medium py-1 px-2">Stock</span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div> <!--end card-->
            </div><!--end col-->
        </div><!--end inner-grid-->
        <!-- footer -->
        <div class="absolute bottom-0 -left-4 -right-4 block print:hidden border-t p-4 h-[52px] dark:border-slate-700/40">
            <div class="container">
                <!-- Footer Start -->
                <footer
                    class="footer bg-transparent  text-center  font-medium text-slate-600 dark:text-slate-400 md:text-left ">
                    &copy;
                    <script>
                        var year = new Date();
                        document.write(year.getFullYear());
                    </script>
                    Robotech
                    <span class="float-right hidden text-slate-600 dark:text-slate-400 md:inline-block">Crafted with <i class="ti ti-heart text-red-500"></i> by
                        Mannatthemes</span>
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