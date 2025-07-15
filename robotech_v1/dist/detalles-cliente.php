<?php
include 'conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de cliente no válido.");
}

$cliente_id = intval($_GET['id']);

$res_pendientes = $conn->query("SELECT * FROM presupuestos WHERE cliente_id = $cliente_id AND estado = 'aceptado' AND convertido = 0");

while ($presupuesto = $res_pendientes->fetch_assoc()) {
    $presupuesto_id = $presupuesto['id'];
    $total = $presupuesto['importe'];
    $fecha_emision = date('Y-m-d');
    $fecha_vencimiento = date('Y-m-d', strtotime('+15 days'));
    $tipo = 'factura';
    $estado_factura = 'pendiente';

    $stmt = $conn->prepare("INSERT INTO facturas (cliente_id, presupuesto_id, tipo, estado, fecha_emision, fecha_vencimiento, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssd", $cliente_id, $presupuesto_id, $tipo, $estado_factura, $fecha_emision, $fecha_vencimiento, $total);
    $stmt->execute();
    $factura_id = $stmt->insert_id;

    $conn->query("UPDATE presupuestos SET convertido = 1, factura_id = $factura_id WHERE id = $presupuesto_id");
}

if (isset($_POST['actualizar_estado']) && $_POST['estado'] === 'aceptado') {
    $presupuesto_id = intval($_POST['presupuesto_id']);

    $res = $conn->query("SELECT * FROM presupuesto WHERE id = $presupuesto_id");
    $presupuesto = $res->fetch_assoc();

    if ($presupuesto && $presupuesto['convertido'] == 0) {
        $cliente_id = $presupuesto['cliente_id'];
        $total = $presupuesto['importe'];
        $fecha_emision = date('Y-m-d');
        $fecha_vencimiento = date('Y-m-d', strtotime('+15 days'));

        $stmt = $conn->prepare("INSERT INTO facturas (cliente_id, presupuesto_id, tipo, estado, fecha_emision, fecha_vencimiento, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $tipo = 'factura';
        $estado_factura = 'pendiente';
        $stmt->bind_param("iissssd", $cliente_id, $presupuesto_id, $tipo, $estado_factura, $fecha_emision, $fecha_vencimiento, $total);
        $stmt->execute();
        $factura_id = $stmt->insert_id;

        $conn->query("UPDATE presupuesto SET convertido = 1, factura_id = $factura_id, estado = 'aceptado' WHERE id = $presupuesto_id");
    }
}

// Datos del cliente
$res_cliente = $conn->query("SELECT * FROM clientes WHERE id = $cliente_id");
$cliente = $res_cliente->fetch_assoc();
if (!$cliente) {
    die("Cliente no encontrado.");
}

// presupuesto
$res_presupuesto = $conn->query("SELECT * FROM presupuestos WHERE cliente_id = $cliente_id");
$total_presupuestos = $res_presupuesto->num_rows;

// Facturas
$res_facturas = $conn->query("SELECT f.*, s.prefijo 
                              FROM facturas f
                              LEFT JOIN series s ON f.serie_id = s.id
                              WHERE f.cliente_id = $cliente_id");
$total_facturas = $res_facturas->num_rows;
//TOTAL DE FACTURAS
$sql_total_facturas = $conn->query("SELECT SUM(total) AS total_facturas FROM facturas WHERE cliente_id = $cliente_id");
$fila = $sql_total_facturas->fetch_assoc();
$total_facturas = $fila['total_facturas'] ?? 0;
//TOTAL DE FACTURAS PAGADAS
$sql_total_facturas_pagadas = $conn->query("SELECT SUM(total) AS total_facturas_pagadas FROM facturas WHERE cliente_id = $cliente_id AND estado = 'pagada'");
$fila_pagadas = $sql_total_facturas_pagadas->fetch_assoc();
$total_facturas_pagadas = $fila_pagadas['total_facturas_pagadas'] ?? 0;
//TOTAL DE FACTURAS PENDIENTES
$sql_total_emitidas_vencidas = $conn->query("SELECT SUM(total) AS total_emitidas_vencidas FROM facturas WHERE cliente_id = $cliente_id AND estado IN ('emitida', 'vencida')");
$fila_emitidas_vencidas = $sql_total_emitidas_vencidas->fetch_assoc();
$total_emitidas_vencidas = $fila_emitidas_vencidas['total_emitidas_vencidas'] ?? 0;

// Proyectos
$res_proyectos = $conn->query("SELECT * FROM proyectos WHERE cliente_id = $cliente_id");
$total_proyectos = $res_proyectos->num_rows;
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
                                <h1 class="font-medium text-3xl block dark:text-slate-100">Ficha Cliente</h1>
                                <ol class="list-reset flex text-sm">
                                    <li><a href="#" class="text-gray-500 dark:text-slate-400">Robotech</a></li>
                                    <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                    <li class="text-gray-500 dark:text-slate-400">Admin</li>
                                    <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                    <li class="text-primary-500 hover:text-primary-600 dark:text-primary-400">Detalles</li>
                                </ol>
                            </div>
                            <div class="flex items-center">
                                <div class="today-date leading-5 mt-2 lg:mt-0 form-input w-auto rounded-md border inline-block border-primary-500/60 dark:border-primary-500/60 text-primary-500 bg-transparent px-3 py-1 focus:outline-none focus:ring-0 placeholder:text-slate-400/70 placeholder:font-normal placeholder:text-sm hover:border-primary-400 focus:border-primary-500 dark:focus:border-primary-500  dark:hover:border-slate-700">
                                    <input type="text" class="dash_date border-0 focus:border-0 focus:outline-none" readonly required="">
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
            <div class="col-span-12 sm:col-span-12 md:col-span-12 lg:col-span-4 xl:col-span-3">
                <div class="">
                    <div class="text-center">
                        <img src="assets/images/users/avatar-1.png" alt="" class="rounded-full mx-auto inline-block">
                        <div class="my-4">
                            <h5 class="text-xxl font-semibold text-slate-700 dark:text-gray-400"><?php echo htmlspecialchars($cliente['nombre']); ?></h5>
                            <span class="block  font-medium text-slate-500"> <?php echo htmlspecialchars($cliente['empresa']); ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Email :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['email']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Telefono :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['telefono']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Direccion :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['direccion']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">NIF :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['nif']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Forma de pago :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['forma_pago']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Plazo de pago :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['plazo_pago']) ?></span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-5 text-end">
                            <span class="dark:text-slate-300">Número de cuenta bancaria :</span>
                        </div><!--end col-->
                        <div class="col-span-12 sm:col-span-12 md:col-span-7">
                            <span class="dark:text-slate-400"> <?= htmlspecialchars($cliente['cuenta_bancaria']) ?></span>
                        </div><!--end col-->
                    </div><!--end grid-->
                    <div class="border-b border-dashed dark:border-slate-700/40 my-3 group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:border-slate-700/40"></div>
                    <h5 class="text-xl font-semibold mb-4 dark:text-slate-300">Notas</h5>
                    <p class="dark:text-slate-400"><?php echo nl2br(htmlspecialchars($cliente['notas'])); ?></p>
                    <div class="mt-5 space-x-2">
                        <a href="clientes.php?editar=<?php echo $cliente['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                    </div>
                </div>
            </div><!--end col-->




            <div class="col-span-12 sm:col-span-12 md:col-span-12 lg:col-span-8 xl:col-span-9">
                <div class="grid  grid-cols-1 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-4 p-4">
                    <div class="bg-primary-500/5 dark:bg-primary-500/10 border border-dashed border-primary-500  rounded-md w-full relative ">
                        <div class="flex-auto p-4 text-center">
                            <span class="inline-flex  justify-center items-center h-14 w-14 rounded-full bg-white dark:bg-gray-900 border border-dashed border-primary-500">
                                <i data-lucide="dollar-sign" class="stroke-primary-500 text-3xl"></i>
                            </span>
                            <h4 class="my-1 font-semibold text-3xl dark:text-slate-200">€ <?= number_format($total_facturas, 2)?></h4>
                            <h6 class="text-gray-800 dark:text-gray-400 mb-0 text-lg font-medium uppercase">Coste Total</h6>
                            <!-- <p class="truncate text-gray-700 dark:text-slate-500 text-sm font-normal">
                                <span class="text-green-500">
                                    <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                </span>
                                Last Payment <span class="text-green-500">$300.00</span>
                            </p> -->
                        </div><!--end card-body-->
                    </div> <!--end card-->
                    <div class="bg-orange-500/5 dark:bg-pink-500/10 border border-dashed border-orange-500  rounded-md w-full relative ">
                        <div class="flex-auto p-4 text-center">

                            <span class="inline-flex  justify-center items-center h-14 w-14 rounded-full bg-white dark:bg-gray-900 border border-dashed border-orange-500">
                                <i data-lucide="shopping-cart" class="stroke-orange-500 text-3xl"></i>
                            </span>
                            <h4 class="my-1 font-semibold text-3xl dark:text-slate-200">€<?= number_format($total_facturas_pagadas, 2) ?></h4>
                            <h6 class="text-gray-800 dark:text-gray-400 text-lg mb-0 font-medium uppercase">Facturas Pagadas</h6>
                            <!-- <p class="truncate text-gray-700 dark:text-slate-500 text-sm font-normal">
                                <span class="bg-green-600/5 text-green-500 text-[11px] font-medium px-2.5 py-0.5 rounded h-5">3</span>
                                Weekly Average
                            </p> -->
                        </div><!--end card-body-->
                    </div> <!--end card-->
                    <div class="bg-purple-500/5 dark:bg-cyan-500/5 border border-dashed border-purple-500  rounded-md w-full relative ">
                        <div class="flex-auto p-4 text-center">

                            <span class="inline-flex  justify-center items-center h-14 w-14 rounded-full bg-white dark:bg-gray-900 border border-dashed border-purple-600">
                                <i data-lucide="circle-dollar-sign" class="stroke-purple-500 text-3xl"></i>
                            </span>
                            <h4 class="my-1 font-semibold text-3xl dark:text-slate-200">€<?= number_format($total_emitidas_vencidas, 2) ?></h4>
                            <h6 class="text-gray-800 dark:text-gray-400 mb-0 text-lg font-medium uppercase">Pendientes de Pago</h6>
                            <!-- <p class="truncate text-gray-700 dark:text-slate-500 text-sm font-normal">
                                Last Date : 26 Nov 2023 <span class="text-red-500">15 Days</span>
                            </p> -->
                        </div><!--end card-body-->
                    </div> <!--end card-->
                </div>
                <div class="w-full relative mb-4">
                    <div class="flex-auto p-0 md:p-4">
                        <div class="mb-4 border-b border-gray-200 dark:border-slate-700" data-fc-type="tab">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" aria-label="Tabs">
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 rounded-t-lg border-b-2 active " id="facturas-tab" data-fc-target="#facturas" type="button" role="tab" aria-controls="facturas" aria-selected="false">Facturas <span class="text-slate-400">(<?= $total_facturas ?>)</span></button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="proyecto-tab" data-fc-target="#proyecto" type="button" role="tab" aria-controls="proyecto" aria-selected="false">Proyectos <span class="text-slate-400">(<?= $total_proyectos ?>)</span></button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="presupuesto-tab" data-fc-target="#presupuesto" type="button" role="tab" aria-controls="presupuesto" aria-selected="false">Presupuestos <span class="text-slate-400">(<?= $total_presupuestos?>)</span></button>
                                </li>
                            </ul>
                        </div>


                        <div id="myTabContent">
                            <div class="active  p-4 bg-gray-50 rounded-lg dark:bg-gray-700/20" id="facturas" role="tabpanel" aria-labelledby="facturas-tab">
                                <div class="grid grid-cols-1 p-0 md:p-4">
                                    <div class="sm:-mx-6 lg:-mx-8">
                                        <div class="relative overflow-x-auto block w-full sm:px-6 lg:px-8">
                                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                                                <thead>
                                                    <tr class="bg-gray-100 text-left">
                                                        <th class="p-2 border">ID</th>
                                                        <th class="p-2 border">Serie</th>
                                                        <th class="p-2 border">Cliente</th>
                                                        <th class="p-2 border">Tipo</th>
                                                        <th class="p-2 border">Estado</th>
                                                        <th class="p-2 border">Emisión</th>
                                                        <th class="p-2 border">Vencimiento</th>
                                                        <th class="p-2 border">Total</th>
                                                        <th class="p-2 border">Recurrente</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($res_facturas as $row): ?>
                                                        <tr>
                                                            <td class='p-2 border'>
                                                                <a href="ver-factura.php?id=<?= $row['id'] ?> " class="text-blue-600 underline">
                                                                    <?= $row['id'] ?>
                                                                </a>
                                                            </td>
                                                            <td class='p-2 border'>
                                                                <?= htmlspecialchars($row['prefijo']) . '-' . $row['numero_factura'] ?>
                                                            </td>
                                                            <td class='p-2 border'><?= isset($row['cliente']) ? htmlspecialchars($row['cliente']) : htmlspecialchars($cliente['nombre']) ?></td>
                                                            <td class='p-2 border'><?= $row['tipo'] ?></td>
                                                            <td class='p-2 border'><?= ucfirst($row['estado']) ?></td>
                                                            <td class='p-2 border'><?= $row['fecha_emision'] ?></td>
                                                            <td class='p-2 border'><?= $row['fecha_vencimiento'] ?></td>
                                                            <td class='p-2 border'>€<?= number_format($row['total'], 2) ?></td>
                                                            <td class='p-2 border'>
                                                                <?= $row['tipo'] === 'recurrente' ? ucfirst($row['periodo_recurrente']) : '—' ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                        </div><!--end div-->
                                    </div><!--end div-->
                                </div><!--end grid-->
                                <div class="flex justify-between mt-4">
                                    <div class="self-center">
                                        <p class="dark:text-slate-400">Showing 1 - 20 of 1,524</p>
                                    </div>
                                    <div class="self-center">
                                        <ul class="inline-flex items-center -space-x-px">
                                            <li>
                                                <a href="#" class=" py-2 px-3 ms-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-left"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                                            </li>
                                            <li>
                                                <a href="#" aria-current="page" class="z-10 py-2 px-3 leading-tight text-brand-600 bg-brand-50 border border-brand-300 hover:bg-brand-100 hover:text-brand-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">2</a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">3</a>
                                            </li>
                                            <li>
                                                <a href="#" class=" py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden p-4 bg-gray-50 rounded-lg dark:bg-gray-800" id="proyecto" role="tabpanel" aria-labelledby="proyecto-tab">
                                <div class="grid grid-cols-1 p-0 md:p-4">
                                    <div class="sm:-mx-6 lg:-mx-8">
                                        <div class="relative overflow-x-auto block w-full sm:px-6 lg:px-8">
                                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm mt-2">
                                                <thead>
                                                    <tr class="bg-gray-100 text-left">
                                                        <th class="p-2 border">ID</th>
                                                        <th class="p-2 border">Nombre</th>
                                                        <th class="p-2 border">Descripción</th>
                                                        <th class="p-2 border">Estado</th>
                                                        <th class="p-2 border">Fecha de inicio</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $res_proyectos->fetch_assoc()): ?>
                                                        <tr>
                                                            <td class="p-2 border">
                                                                <a href="ver-proyecto.php?id=<?= $row['id'] ?>" class="text-blue-600 underline">
                                                                    <?= $row['id'] ?>
                                                                </a>
                                                            </td>
                                                            <td class="p-2 border"><?= htmlspecialchars($row['nombre']) ?></td>
                                                            <td class="p-2 border"><?= htmlspecialchars($row['descripcion']) ?></td>
                                                            <td class="p-2 border"><?= ucfirst($row['estado']) ?></td>
                                                            <td class="p-2 border"><?= $row['fecha_inicio'] ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div><!--end div-->
                                    </div><!--end div-->
                                </div><!--end grid-->
                                <div class="flex justify-between">
                                    <div class="self-center">
                                        <p class="dark:text-slate-400">Showing 1 - 20 of 1,524</p>
                                    </div>
                                    <div class="self-center">
                                        <ul class="inline-flex items-center -space-x-px">
                                            <li>
                                                <a href="#" class=" py-2 px-3 ms-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-left"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                                            </li>
                                            <li>
                                                <a href="#" aria-current="page" class="z-10 py-2 px-3 leading-tight text-brand-600 bg-brand-50 border border-brand-300 hover:bg-brand-100 hover:text-brand-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">2</a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">3</a>
                                            </li>
                                            <li>
                                                <a href="#" class=" py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden p-4 bg-gray-50 rounded-lg dark:bg-gray-800" id="presupuesto" role="tabpanel" aria-labelledby="presupuesto-tab">
                                <div class="grid grid-cols-1 p-0 md:p-4">
                                    <div class="sm:-mx-6 lg:-mx-8">
                                        <div class="relative overflow-x-auto block w-full sm:px-6 lg:px-8">
                                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm mt-2">
                                                <thead>
                                                    <tr class="bg-gray-100 text-left">
                                                        <th class="p-2 border">ID</th>
                                                        <th class="p-2 border">Estado</th>
                                                        <th class="p-2 border">Fecha de creación</th>
                                                        <th class="p-2 border">Fecha de validez</th>
                                                        <th class="p-2 border">Importe</th>
                                                        <th class="p-2 border">¿Convertido?</th>
                                                        <th class="p-2 border">Factura ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $res_presupuesto->fetch_assoc()): ?>
                                                        <tr>
                                                            <td class="p-2 border">
                                                                <a href="ver-presupuesto.php?id=<?= $row['id'] ?>" class="text-blue-600 underline">
                                                                    <?= $row['id'] ?>
                                                                </a>
                                                            </td>
                                                            <td class="p-2 border"><?= ucfirst($row['estado']) ?></td>
                                                            <td class="p-2 border"><?= $row['fecha_creacion'] ?></td>
                                                            <td class="p-2 border"><?= $row['fecha_validez'] ?></td>
                                                            <td class="p-2 border">€<?= number_format($row['importe'], 2) ?></td>
                                                            <td class="p-2 border"><?= $row['convertido'] ? 'Sí' : 'No' ?></td>
                                                            <td class="p-2 border">
                                                                <?= $row['factura_id'] ? $row['factura_id'] : '—' ?>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>

                                        </div><!--end div-->
                                    </div><!--end div-->
                                </div><!--end grid-->
                                <div class="flex justify-between">
                                    <div class="self-center">
                                        <p class="dark:text-slate-400">Showing 1 - 20 of 1,524</p>
                                    </div>
                                    <div class="self-center">
                                        <ul class="inline-flex items-center -space-x-px">
                                            <li>
                                                <a href="#" class=" py-2 px-3 ms-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-left"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                                            </li>
                                            <li>
                                                <a href="#" aria-current="page" class="z-10 py-2 px-3 leading-tight text-brand-600 bg-brand-50 border border-brand-300 hover:bg-brand-100 hover:text-brand-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">2</a>
                                            </li>
                                            <li>
                                                <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">3</a>
                                            </li>
                                            <li>
                                                <a href="#" class=" py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                    <i class="icofont-simple-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div> <!--end grid-->


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
    </div><!--end page-wrapper-->
    </div><!--end /div-->

    <!-- JAVASCRIPTS -->
    <!-- <div class="menu-overlay"></div> -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

    <script src="assets/js/app.js"></script>
    <!-- JAVASCRIPTS -->
</body>

</html>