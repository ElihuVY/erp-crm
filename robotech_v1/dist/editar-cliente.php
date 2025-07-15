<?php
include 'conexion.php';

$id = intval($_GET['id']);

// Get client data
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

// Save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $direccion = $_POST['direccion'];
    $notas = $_POST['notas'];
    $favorito = isset($_POST['favorito']) ? 1 : 0;
    $cuenta_contable = $_POST['cuenta_contable'];
    $nif = $_POST['nif'];
    $forma_pago = $_POST['forma_pago'];
    $plazo_pago = $_POST['plazo_pago'];
    $cuenta_bancaria = $_POST['cuenta_bancaria'];

    $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, empresa = ?, direccion = ?, notas = ?, favorito = ?, cuenta_contable = ?, nif = ?, forma_pago = ?, plazo_pago = ?, cuenta_bancaria = ? WHERE id = ?");
    $stmt->bind_param("ssssssissisii", $nombre, $email, $telefono, $empresa, $direccion, $notas, $favorito, $cuenta_contable, $nif, $forma_pago, $plazo_pago, $cuenta_bancaria, $id);
    $stmt->execute();

    header("Location: clientes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- CSS -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">

    <style>
        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }

        .py-30 {
            padding-left: 15px;
        }
    </style>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <?php include 'menu.php'; ?>

    <div class="xl:w-full">
        <main class="pt-[120px] px-4 pb-24 lg:ms-[260px]">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <nav class="flex mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="inicio.php" class="text-gray-500 hover:text-gray-700 inline-flex items-center">
                                    <i class="icofont-home w-4 h-4 mr-2"></i>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="icofont-simple-right text-gray-400"></i>
                                    <a href="clientes.php" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Clientes</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <i class="icofont-simple-right text-gray-400"></i>
                                    <span class="ml-1 text-gray-400 md:ml-2">Editar</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="max-w-4xl mx-auto">
                <!-- Main Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between px-6 py-2">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            ✏️ Editar Cliente
                        </h1>
                        <div class="bg-green-100 text-green-800 text-lg font-semibold px-6 py-3 rounded-full shadow-sm">
                            ID: #<?= $cliente['id'] ?>
                        </div>
                    </div>
                    <!-- Card Header -->
                    <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                                <i class="icofont-user text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Información del Cliente</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Modifica los datos del cliente</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-12">
                        <form method="POST" class="space-y-12">
                            <!-- Información Personal -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Nombre -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-user mr-1"></i>
                                        Nombre *
                                    </label>
                                    <input type="text" id="nombre" name="nombre"
                                        value="<?= htmlspecialchars($cliente['nombre']) ?>" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Email -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-email mr-1"></i>
                                        Email
                                    </label>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($cliente['email']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Teléfono -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-phone mr-1"></i>
                                        Teléfono
                                    </label>
                                    <input type="tel" id="telefono" name="telefono"
                                        value="<?= htmlspecialchars($cliente['telefono']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Empresa -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="empresa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-building mr-1"></i>
                                        Empresa
                                    </label>
                                    <input type="text" id="empresa" name="empresa"
                                        value="<?= htmlspecialchars($cliente['empresa']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="mb-6 pt-4 px-4">
                                <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                    <i class="icofont-location-pin mr-1"></i>
                                    Dirección
                                </label>
                                <textarea id="direccion" name="direccion" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm"><?= htmlspecialchars($cliente['direccion']) ?></textarea>
                            </div>

                            <!-- Información Fiscal -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- NIF -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="nif" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-id mr-1"></i>
                                        NIF
                                    </label>
                                    <input type="text" id="nif" name="nif"
                                        value="<?= htmlspecialchars($cliente['nif']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Cuenta Contable -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="cuenta_contable" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-bank mr-1"></i>
                                        Cuenta Contable
                                    </label>
                                    <input type="text" id="cuenta_contable" name="cuenta_contable"
                                        value="<?= htmlspecialchars($cliente['cuenta_contable']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <!-- Información de Pago -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Forma de Pago -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="forma_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-pay mr-1"></i>
                                        Forma de Pago
                                    </label>
                                    <select id="forma_pago" name="forma_pago"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                        <option value="1" <?= $cliente['forma_pago'] == 1 ? 'selected' : '' ?>>Transferencia</option>
                                        <option value="2" <?= $cliente['forma_pago'] == 2 ? 'selected' : '' ?>>Efectivo</option>
                                        <option value="3" <?= $cliente['forma_pago'] == 3 ? 'selected' : '' ?>>Tarjeta</option>
                                    </select>
                                </div>

                                <!-- Plazo de Pago -->
                                <div class="mb-6 pt-4 px-4">
                                    <label for="plazo_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <i class="icofont-calendar mr-1"></i>
                                        Plazo de Pago (días)
                                    </label>
                                    <input type="number" id="plazo_pago" name="plazo_pago"
                                        value="<?= htmlspecialchars($cliente['plazo_pago']) ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <!-- Cuenta Bancaria -->
                            <div class="mb-6 pt-4 px-4">
                                <label for="cuenta_bancaria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                    <i class="icofont-bank-alt mr-1"></i>
                                    Cuenta Bancaria (IBAN)
                                </label>
                                <input type="text" id="cuenta_bancaria" name="cuenta_bancaria"
                                    value="<?= htmlspecialchars($cliente['cuenta_bancaria']) ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Notas -->
                            <div class="mb-6 pt-4 px-4">
                                <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                    <i class="icofont-notepad mr-1"></i>
                                    Notas
                                </label>
                                <textarea id="notas" name="notas" rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm"><?= htmlspecialchars($cliente['notas']) ?></textarea>
                            </div>

                            <!-- Cliente Favorito -->
                            <div class="mb-4 pt-4 px-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="favorito" value="1"
                                        <?= $cliente['favorito'] ? 'checked' : '' ?>
                                        class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-gray-700">Marcar como cliente favorito</span>
                                </label>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-between items-center mt-16 mb-8 pt-8 px-4">
                                <a href="clientes.php"
                                    class="px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="icofont-arrow-left mr-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="icofont-save mr-2"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

               
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/pages/analytics-index.init.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>