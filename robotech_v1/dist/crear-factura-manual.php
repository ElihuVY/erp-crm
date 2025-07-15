<?php
include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];


// Devolver series por organización en JSON
if (isset($_GET['get-series'])) {
    $org_id = intval($_GET['get-series']);
    $sql = "SELECT s.id, s.nombre 
            FROM series s
            JOIN org_series os ON s.id = os.serie_id
            WHERE os.organizacion_id = $org_id";
    $result = $conn->query($sql);

    $series = [];
    while ($row = $result->fetch_assoc()) {
        $series[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($series);
    exit;
}


// Organizaciones disponibles para el usuario
$organizaciones = $conn->query("SELECT o.* 
    FROM organizaciones o 
    JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
    WHERE uo.usuario_id = '$usuario_id'");

// Agregar endpoint para búsqueda de organizaciones por AJAX
if (isset($_GET['buscar_organizacion'])) {
    $busqueda = $conn->real_escape_string($_GET['buscar_organizacion']);
    $sql = "SELECT o.id, o.nombre 
            FROM organizaciones o 
            JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
            WHERE uo.usuario_id = '$usuario_id' AND o.nombre LIKE '%$busqueda%' 
            ORDER BY o.nombre ASC 
            LIMIT 10";
    $result = $conn->query($sql);
    $organizaciones_encontradas = [];
    while ($row = $result->fetch_assoc()) {
        $organizaciones_encontradas[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($organizaciones_encontradas);
    exit;
}

// Endpoint para obtener organización por ID
if (isset($_GET['buscar_organizacion_id'])) {
    $id = intval($_GET['buscar_organizacion_id']);
    $sql = "SELECT o.id, o.nombre 
            FROM organizaciones o 
            JOIN usuario_organizacion uo ON o.id = uo.organizacion_id 
            WHERE uo.usuario_id = '$usuario_id' AND o.id = $id";
    $result = $conn->query($sql);
    $organizacion = $result->fetch_assoc() ?: ['id' => '', 'nombre' => ''];
    header('Content-Type: application/json');
    echo json_encode($organizacion);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $organizacion_id = $_POST['organizacion_id'];
    $serie_id = $_POST['serie_id'] ?? null;
    $numero_factura = $_POST['numero_factura'] ?? null;
    $tipo = $_POST['tipo'];
    $estado = 'emitida'; // Establecer estado como "emitida" por defecto
    $fecha_emision = $_POST['fecha_emision'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
    $iva = $_POST['iva'];
    $irpf = $_POST['irpf'];
    $impuestos_extra = $_POST['impuestos_extra'] ?? null;
    $periodo = $_POST['periodo_recurrente'] ?? null;
    $hasta = $_POST['recurrente_hasta'] ?? null;
    $notas = $_POST['notas'] ?? null;

    $productos = $_POST['productos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];


    $total = 0;
    $productos_data = [];

    // 🧮 Calcular totales y preparar los productos
    foreach ($productos as $index => $producto_id) {
        $cantidad = $cantidades[$index];
        $stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ? AND organizacion_id = ?");
        $stmt->bind_param("ii", $producto_id, $organizacion_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $subtotal = $row['precio'] * $cantidad;
            $total += $subtotal;
            $productos_data[] = [
                'id' => $producto_id,
                'descripcion' => $row['nombre'],
                'cantidad' => $cantidad,
                'precio' => $row['precio']
            ];
        }
    }

    // 🔢 Generar número de factura si es automática
    if ($serie_id) {
        $res = $conn->query("SELECT * FROM series WHERE id = $serie_id LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $serie = $res->fetch_assoc();

            if (!$serie['numeracion_manual']) {
                $año = date('Y');
                $query = "SELECT MAX(CAST(numero_factura AS UNSIGNED)) as max_num FROM facturas WHERE serie_id = $serie_id";
                if ($serie['reiniciar_anual']) {
                    $query .= " AND YEAR(fecha_emision) = '$año'";
                }

                $res_max = $conn->query($query);
                $max = $res_max->fetch_assoc()['max_num'] ?? 0;
                $numero_factura = $max + 1;
            }
        } else {
            die("❌ Serie de facturación no válida.");
        }
    }

    // 🧾 Insertar la factura
    $stmt = $conn->prepare("INSERT INTO facturas 
        (cliente_id, organizacion_id, serie_id, numero_factura, tipo, estado, fecha_emision, fecha_vencimiento, total, periodo_recurrente, recurrente_hasta, iva, irpf, impuestos_extra, notas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssssdssddss", $cliente_id, $organizacion_id, $serie_id, $numero_factura, $tipo, $estado, $fecha_emision, $fecha_vencimiento, $total, $periodo, $hasta, $iva, $irpf, $impuestos_extra, $notas);
    $stmt->execute();
    $factura_id = $conn->insert_id;

    // 🔁 Si es recurrente, vinculamos recurrente_id
    if ($tipo === 'recurrente') {
        $conn->query("UPDATE facturas SET recurrente_id = $factura_id WHERE id = $factura_id");
    }

    $urlFactura = "http://localhost/robotech_v1/dist/ver-factura.php?id=$factura_id";

    require_once __DIR__ . '/libs/phpqrcode/qrlib.php';

    $qrPath = __DIR__ . "/qr_facturas/factura_$factura_id.png";
    QRcode::png($urlFactura, $qrPath, QR_ECLEVEL_L, 8);

    // 📦 Insertar productos
    foreach ($productos_data as $item) {
        $stmt = $conn->prepare("INSERT INTO factura_items 
            (factura_id, producto_id, descripcion, cantidad, precio_unitario) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisid", $factura_id, $item['id'], $item['descripcion'], $item['cantidad'], $item['precio']);
        $stmt->execute();
    }

    // ✅ Redirigir a ver la factura creada
    header("Location: ver-factura.php?id=$factura_id");

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
    <style>
        .form-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 4px solid #3b82f6;
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .form-input:hover {
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(107, 114, 128, 0.3);
        }

        .plazo-pago-display {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            color: #065f46;
        }

        .section-title {
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .loading-spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
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
    </style>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <!-- leftbar-tab-menu -->

    <?php
    include 'menu.php';
    ?>

    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center py-4 w-full">
                <div class="w-full">
                    <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
                        <div class="max-w-5xl mx-auto">

                            <!-- Header -->
                            <div class="bg-white p-6 rounded-t-2xl shadow-lg border-b-2 border-blue-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-bold section-title">Crear Nueva Factura</h1>
                                        <p class="text-gray-600 mt-1">Complete los datos para generar la factura</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-8 rounded-b-2xl shadow-lg">

                                <form method="POST" class="space-y-8">

                                    <div class="form-section p-6 rounded-xl">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                                            </svg>
                                            Información de la Organización
                                        </h3>
                                        <div class="grid md:grid-cols-2 gap-6">
                                            <div>
                                                <label>Organización emisora:
                                                    <select name="organizacion_id" id="organizacion" class="form-input w-full p-3 rounded-lg" required>
                                                        <option value="">Selecciona una organización</option>
                                                        <?php while ($org = $organizaciones->fetch_assoc()): ?>
                                                            <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['nombre']) ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </label>

                                            </div>
                                            <div>
                                                <!-- Serie -->
                                                <label>Serie de facturación:
                                                    <select name="serie_id" id="serie_id" class="form-input w-full p-3 rounded-lg" required onchange="mostrarCampoNumero(); cargarDatosSerie();">
                                                        <option value="">Selecciona una serie</option>
                                                    </select>
                                                </label>

                                            </div>
                                        </div>

                                    </div>

                                    <!-- Sección: Información del Cliente -->
                                    <div class="form-section p-6 rounded-xl">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Información del Cliente
                                        </h3>


                                        <div class="grid md:grid-cols-2 gap-6">
                                            <div>
                                                <!-- Cliente -->
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente:
                                                    <select name="cliente_id" id="cliente_id" class="form-input w-full p-3 rounded-lg" required onchange="cargarPlazoPago()">
                                                        <option value="">Selecciona un cliente</option>
                                                        <?php
                                                        $res = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
                                                        while ($cli = $res->fetch_assoc()) {
                                                            echo "<option value='{$cli['id']}'>" . htmlspecialchars($cli['nombre']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </label>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Plazo de pago:
                                                    <div class="relative">
                                                        <input type="text" name="plazo_pago" id="plazo_pago"
                                                            class="plazo-pago-display w-full p-3 rounded-lg font-medium"
                                                            readonly placeholder="Se completará automáticamente">
                                                        <div id="loading-plazo" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                                            <div class="loading-spinner"></div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <script>
                                                    // Establecer la fecha de emisión con la fecha actual
                                                    document.addEventListener('DOMContentLoaded', () => {
                                                        const today = new Date().toISOString().split('T')[0];
                                                        document.getElementById('fecha_emision').value = today;
                                                    });

                                                    // Modificamos cargarPlazoPago para calcular la fecha de vencimiento automáticamente
                                                    function cargarPlazoPago() {
                                                        const clienteId = document.getElementById('cliente_id').value;
                                                        const loadingIndicator = document.getElementById('loading-plazo');
                                                        const plazoPagoInput = document.getElementById('plazo_pago');
                                                        const fechaEmisionInput = document.getElementById('fecha_emision');
                                                        const fechaVencimientoInput = document.getElementById('fecha_vencimiento');

                                                        if (!clienteId) {
                                                            plazoPagoInput.value = '';
                                                            fechaVencimientoInput.value = '';
                                                            return;
                                                        }

                                                        loadingIndicator.classList.remove('hidden');

                                                        fetch(`get_plazo_pago.php?cliente_id=${clienteId}`)
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                const plazo = data.plazo_pago || '';
                                                                plazoPagoInput.value = plazo;

                                                                // Calcular fecha de vencimiento solo si hay un número de días
                                                                const match = plazo.match(/(\d+)\s*d[ií]as?/i);
                                                                if (match) {
                                                                    const dias = parseInt(match[1], 10);
                                                                    const fechaEmision = new Date(fechaEmisionInput.value);
                                                                    const fechaVencimiento = new Date(fechaEmision);
                                                                    fechaVencimiento.setDate(fechaVencimiento.getDate() + dias);
                                                                    fechaVencimientoInput.value = fechaVencimiento.toISOString().split('T')[0];
                                                                } else {
                                                                    fechaVencimientoInput.value = '';
                                                                }
                                                            })
                                                            .catch(error => {
                                                                console.error('Error:', error);
                                                                plazoPagoInput.value = 'Error al cargar plazo de pago';
                                                                fechaVencimientoInput.value = '';
                                                            })
                                                            .finally(() => {
                                                                loadingIndicator.classList.add('hidden');
                                                            });
                                                    }
                                                </script>
                                            </div>
                                        </div>


                                    </div>

                                    <!-- Sección: Detalles de la Factura -->

                                    <div class="form-section p-6 rounded-xl">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            Detalles de la Factura
                                        </h3>

                                        <div class="grid md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tipo *
                                                </label>
                                                <select name="tipo" id="tipo" class="form-input w-full p-3 rounded-lg" required onchange="toggleCamposRecurrente()">
                                                    <option value="unica">Única</option>
                                                    <option value="recurrente">Recurrente</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Estado *
                                                </label>
                                                <select name="estado" class="form-input w-full p-3 rounded-lg" required>
                                                    <option value="borrador">Borrador</option>
                                                    <option value="emitida" selected>Emitida</option>
                                                    <option value="pagada">Pagada</option>
                                                    <option value="vencida">Vencida</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid md:grid-cols-2 gap-6 mt-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Fecha de emisión *
                                                </label>
                                                <input type="date" name="fecha_emision" id="fecha_emision" class="form-input w-full p-3 rounded-lg" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Fecha de vencimiento
                                                </label>
                                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-input w-full p-3 rounded-lg">
                                            </div>
                                        </div>


                                    </div>
                                    <!-- Sección: Información Fiscal -->
                                    <div class="form-section p-6 rounded-xl">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            Información Fiscal
                                        </h3>

                                        <div class="grid md:grid-cols-3 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    IVA (%)
                                                </label>
                                                <input type="number" step="0.01" name="iva" id="iva" value="21.00"
                                                    class="form-input w-full p-3 rounded-lg">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    IRPF (%)
                                                </label>
                                                <input type="number" step="0.01" name="irpf" id="irpf" value="15.00"
                                                    class="form-input w-full p-3 rounded-lg">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Impuestos adicionales
                                                </label>
                                                <input type="text" name="impuestos_extra" id="impuestos_extra"
                                                    class="form-input w-full p-3 rounded-lg" placeholder="Ej. Recargo 5%">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sección: Recurrencia (oculta por defecto) -->
                                    <div id="campos-recurrente" class="form-section p-6 rounded-xl fade-in" style="display:none">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Configuración Recurrente
                                        </h3>

                                        <div class="grid md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Periodo recurrente
                                                </label>
                                                <select name="periodo_recurrente" class="form-input w-full p-3 rounded-lg">
                                                    <option value="">-- Selecciona --</option>
                                                    <option value="mensual">Mensual</option>
                                                    <option value="trimestral">Trimestral</option>
                                                    <option value="anual">Anual</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Recurrente hasta
                                                </label>
                                                <input type="date" name="recurrente_hasta" class="form-input w-full p-3 rounded-lg">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección: Productos -->
                                    <div class="form-section p-6 rounded-xl">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            Productos y Servicios
                                        </h3>
                                        <div id="productos-container" class="space-y-4">
                                            <div class="border p-4 rounded bg-gray-50">
                                                <h3 class="font-semibold mb-2">🧺 Productos</h3>

                                                <!-- Este contenedor es donde se agregan dinámicamente los productos -->
                                                <div id="productos-list" class="space-y-2">
                                                    <div>
                                                        <label>Producto:
                                                            <select name="productos[]" class="border p-2 w-full">
                                                                <option value="">Selecciona un producto</option>
                                                                <?php
                                                                $productos = $conn->query("SELECT id, nombre FROM productos WHERE organizacion_id IN 
                                                                (SELECT organizacion_id FROM usuario_organizacion WHERE usuario_id = '$usuario_id')
                                                                ORDER BY nombre ASC");
                                                                while ($p = $productos->fetch_assoc()) {
                                                                    echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nombre']) . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </label>
                                                        <label>Cantidad:
                                                            <input type="number" name="cantidades[]" class="border p-2 w-full" min="1" value="1">
                                                        </label>
                                                        <hr>
                                                    </div>
                                                </div>

                                                <!-- Este botón debe quedar fuera del contenedor que se borra -->
                                                <button type="button" onclick="agregarProducto()"
                                                    class="mt-4 bg-green-500 text-white px-6 py-3 rounded-lg">
                                                    Añadir nuevo producto
                                                </button>
                                            </div>

                                        </div>

                                        <div class="form-section p-6 rounded-xl">
                                            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Notas Adicionales
                                            </h3>

                                            <textarea name="notas" rows="4" class="form-input w-full p-4 rounded-lg"
                                                placeholder="Ej. En esta factura se necesita ..."></textarea>
                                        </div>

                                        <div class="flex justify-between items-center mt-6">
                                            <a href="facturas.php" class="btn-secondary text-white px-4 py-2 rounded-lg font-semibold w-full sm:w-auto text-center">
                                                ← Cancelar
                                            </a>
                                            <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg font-semibold w-full sm:w-auto flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Crear Factura
                                            </button>
                                        </div>

                                        <script>
                                            document.getElementById('organizacion').addEventListener('change', function() {
                                                const container = document.getElementById('productos-list');
                                                container.innerHTML = '';
                                                agregarProducto(); // Solo borra y repuebla los inputs dinámicos, no el botón
                                            });
                                        </script>


                                </form>
                            </div>
                        </div>
                    </main>

                    <script>
                        function mostrarCampoNumero() {
                            const manual = document.getElementById('serie_id').selectedOptions[0].dataset.manual === '1';
                            document.getElementById('campo-numero').style.display = manual ? 'block' : 'none';
                        }

                        async function cargarSeries() {
                            const orgId = document.getElementById('organizacion_id').value;
                            const res = await fetch(`get-series.php?organizacion_id=${orgId}`);
                            const series = await res.json();

                            const serieSelect = document.getElementById('serie_id');
                            serieSelect.innerHTML = '';
                            series.forEach(s => {
                                const opt = document.createElement('option');
                                opt.value = s.id;
                                opt.textContent = s.etiqueta;
                                opt.dataset.manual = s.manual;
                                serieSelect.appendChild(opt);
                            });

                            mostrarCampoNumero();
                        }



                        function toggleCamposRecurrente() {
                            const tipo = document.getElementById('tipo').value;
                            document.getElementById('campos-recurrente').style.display = tipo === 'recurrente' ? 'block' : 'none';
                        }

                        window.onload = cargarSeries;
                    </script>




                </div>
            </div>


        </div><!--end container-->
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
    <script>
        document.getElementById('organizacion').addEventListener('change', function() {
            const orgId = this.value;
            const serieSelect = document.getElementById('serie_id');

            if (!orgId) {
                serieSelect.innerHTML = '<option value="">Selecciona una serie</option>';
                return;
            }

            serieSelect.innerHTML = '<option value="">Cargando series...</option>';

            fetch('?get-series=' + orgId)
                .then(res => res.json())
                .then(data => {
                    serieSelect.innerHTML = '<option value="">Selecciona una serie</option>';
                    data.forEach(serie => {
                        const option = document.createElement('option');
                        option.value = serie.id;
                        option.textContent = serie.nombre;
                        serieSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    serieSelect.innerHTML = '<option value="">Error al cargar</option>';
                    console.error('Error:', error);
                });
        });
    </script>
    <script>
        function agregarProducto() {
            const container = document.getElementById('productos-list');

            const item = document.createElement('div');
            item.classList.add('producto-item', 'space-y-2');

            const productoLabel = document.createElement('label');
            productoLabel.textContent = 'Producto: ';
            const select = document.createElement('select');
            select.name = 'productos[]';
            select.className = 'border p-2 w-full';

            const orgId = document.getElementById('organizacion').value;

            fetch('get-productos.php?org_id=' + orgId)
                .then(res => res.json())
                .then(productos => {
                    select.innerHTML = '';
                    productos.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.nombre;
                        select.appendChild(option);
                    });
                });

            productoLabel.appendChild(select);
            item.appendChild(productoLabel);

            const cantidadLabel = document.createElement('label');
            cantidadLabel.textContent = 'Cantidad: ';
            const cantidadInput = document.createElement('input');
            cantidadInput.type = 'number';
            cantidadInput.name = 'cantidades[]';
            cantidadInput.className = 'border p-2 w-full';
            cantidadInput.min = '1';
            cantidadInput.value = '1';
            cantidadLabel.appendChild(cantidadInput);
            item.appendChild(cantidadLabel);

            const hr = document.createElement('hr');
            item.appendChild(hr);

            container.appendChild(item);
        }
    </script>



</body>

</html>
<script>
    document.querySelectorAll('.estado-select').forEach(select => {
        select.addEventListener('change', async function() {
            const facturaId = this.dataset.id;
            const nuevoEstado = this.value;
            const mensajeError = document.getElementById('mensaje-error-' + facturaId);

            const formData = new FormData();
            formData.append('factura_id', facturaId);
            formData.append('estado', nuevoEstado);

            const res = await fetch('ajax-cambiar-estado.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                mensajeError.classList.add('hidden');
                mensajeError.innerText = '';
                location.reload();
            } else {
                mensajeError.classList.remove('hidden');
                mensajeError.innerText = data.message;
                this.value = data.estado_actual;
            }
        });
    });
</script>
<script>
    function toggleCamposRecurrente() {
        const tipo = document.getElementById('tipo').value;
        const campos = document.getElementById('campos-recurrente');
        campos.style.display = tipo === 'recurrente' ? 'block' : 'none';
    }
</script>
<script>
    function cargarSeries() {
        const orgId = document.getElementById('organizacion_id').value;
        const selectSerie = document.getElementById('serie_id');
        selectSerie.innerHTML = '<option>Cargando...</option>';

        fetch(`get-series.php?organizacion_id=${orgId}`)
            .then(res => res.text())
            .then(html => {
                selectSerie.innerHTML = html;
            })
            .catch(() => {
                selectSerie.innerHTML = '<option>Error al cargar series</option>';
            });
    }
</script>
<script>
    // Add event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const serieSelect = document.getElementById('serie_id');
        if (serieSelect) {
            serieSelect.addEventListener('change', handleSerieChange);
        }
    });

    // Handle serie selection change
    function handleSerieChange() {
        const serieId = this.value;

        // Clear fields if no serie selected
        if (!serieId) {
            clearFields();
            return;
        }

        // Show loading state
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';

        // Fetch serie data
        fetch(`ajax-datos-serie.php?id=${serieId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data) {
                    throw new Error('No data received');
                }
                updateFields(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading serie data. Please try again.');
                clearFields();
            })
            .finally(() => {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            });
    }

    // Update form fields with serie data
    function updateFields(data) {
        // Update tax fields
        const fields = {
            'iva': data.iva || '',
            'irpf': data.irpf || '',
            'impuestos_extra': data.impuestos_extra || ''
        };

        Object.entries(fields).forEach(([name, value]) => {
            const input = document.querySelector(`input[name="${name}"]`);
            if (input) input.value = value;
        });

        // Toggle invoice number field visibility
        const numeroField = document.getElementById('campo-numero');
        if (numeroField) {
            numeroField.style.display = data.numeracion_manual == 1 ? 'block' : 'none';
        }
    }

    // Clear all form fields
    function clearFields() {
        const fields = ['iva', 'irpf', 'impuestos_extra'];
        fields.forEach(field => {
            const input = document.querySelector(`input[name="${field}"]`);
            if (input) input.value = '';
        });

        const numeroField = document.getElementById('campo-numero');
        if (numeroField) numeroField.style.display = 'none';
    }
</script>