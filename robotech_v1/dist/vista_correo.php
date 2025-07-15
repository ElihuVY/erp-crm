<?php
include 'conexion.php';

// Verificamos si se recibieron facturas seleccionadas
if (!isset($_POST['facturas']) || empty($_POST['facturas'])) {
    echo "No se seleccionaron facturas.";
    exit;
}

$facturas_ids = $_POST['facturas']; // Array de IDs

// Convertimos a enteros para evitar inyección SQL
$ids_filtrados = array_map('intval', $facturas_ids);
$ids_sql = implode(',', $ids_filtrados);

// Consulta a la base de datos para obtener las facturas
$query = "SELECT f.*, c.email AS email_cliente, c.nombre AS nombre_cliente 
          FROM facturas f 
          JOIN clientes c ON f.cliente_id = c.id 
          WHERE f.id IN ($ids_sql)";
$resultado = mysqli_query($conn, $query);

if (!$resultado) {
    echo "Error al obtener las facturas: " . mysqli_error($conn);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redactar Correo - Facturas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />


    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://cdn.jsdelivr.net/npm/@frostui/tailwindcss@1.0.0/dist/frost.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@frostui/tailwindcss@1.0.0/dist/frost.min.js"></script>
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
                    <main class="lg:ms-[60px] pt-[10px] px-4 pb-16">
                        <!-- Email Header -->
                        <div class="bg-white border-b shadow-sm max-w-4xl mx-auto">
                            <div class="max-w-4xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                            <i data-lucide="mail" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <div>
                                            <h1 class="text-xl font-semibold text-gray-900">Redactar Correo</h1>
                                            <p class="text-sm text-gray-500">Envío de facturas por email</p>
                                        </div>
                                    </div>
                                    <button onclick="window.close()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                                        Volver
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email Container -->
                        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">

                                <!-- Email Toolbar -->
                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <i data-lucide="paperclip" class="w-4 h-4 text-gray-400"></i>
                                            <span class="text-sm text-gray-600"><?php echo count($ids_filtrados); ?> facturas adjuntas</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                                            <span class="text-sm text-gray-600">
                                                <?php
                                                $emails = [];
                                                mysqli_data_seek($resultado, 0);
                                                while ($row = mysqli_fetch_assoc($resultado)) {
                                                    if (!in_array($row['email_cliente'], $emails)) {
                                                        $emails[] = $row['email_cliente'];
                                                    }
                                                }
                                                echo count($emails) . " destinatario" . (count($emails) > 1 ? "s" : "");
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" class="text-gray-400 hover:text-gray-600">
                                            <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Email Form -->
                                <form action="enviar-factura.php" method="POST" class="divide-y divide-gray-200">

                                    <!-- From Field -->
                                    <div class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <label class="text-sm font-medium text-gray-700 w-16">De:</label>
                                            <div class="flex-1 relative">
                                                <input type="email"
                                                    id="remitente"
                                                    name="remitente"
                                                    required
                                                    placeholder="tu@empresa.com"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- To Field -->
                                    <div class="px-6 py-4">
                                        <div class="flex items-start space-x-3">
                                            <label class="text-sm font-medium text-gray-700 w-16 pt-2">Para:</label>
                                            <div class="flex-1">
                                                <div class="relative">
                                                    <input type="email"
                                                        id="destinatario"
                                                        name="destinatario"
                                                        required
                                                        placeholder="destinatario@email.com"
                                                        value="<?php echo htmlspecialchars(implode(',', $emails)); ?>"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                        <i data-lucide="at-sign" class="w-4 h-4 text-gray-400"></i>
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Separar múltiples destinatarios con comas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subject Field -->
                                    <div class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <label class="text-sm font-medium text-gray-700 w-16">Asunto:</label>
                                            <div class="flex-1 relative">
                                                <input type="text"
                                                    id="asunto"
                                                    name="asunto"
                                                    required
                                                    value="Facturas pendientes"
                                                    placeholder="Asunto del correo"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <i data-lucide="hash" class="w-4 h-4 text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 py-4 bg-blue-50 border-l-4 border-blue-400">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i data-lucide="paperclip" class="w-5 h-5 text-blue-400"></i>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h4 class="text-sm font-medium text-blue-800 mb-2">Facturas adjuntas</h4>
                                                <div class="space-y-2">
                                                    <?php
                                                    mysqli_data_seek($resultado, 0);
                                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                                        echo "<div class='flex items-center justify-between p-2 bg-white rounded border border-blue-200'>";
                                                        echo "<div class='flex items-center space-x-2'>";
                                                        echo "<i data-lucide='file-text' class='w-4 h-4 text-gray-400'></i>";
                                                        echo "<div class='flex items-center space-x-2'>";
                                                        echo "<span class='text-sm text-gray-700'>";
                                                        echo "<a href='descargar-factura.php?id={$row['id']}' target='_blank' class='text-blue-600 hover:text-blue-800 hover:underline font-medium'>Factura #{$row['id']}</a>";
                                                        echo " - {$row['nombre_cliente']}";
                                                        echo "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                        echo "<span class='text-sm font-medium text-green-600'>€" . number_format($row['total'], 2) . "</span>";
                                                        echo "</div>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Body -->
                                    <div class="px-6 py-4">
                                        <div class="space-y-4">
                                            <!-- Email Formatting Toolbar -->
                                            <div class="flex items-center space-x-2 pb-3 border-b border-gray-200">
                                                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded">
                                                    <i data-lucide="bold" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded">
                                                    <i data-lucide="italic" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded">
                                                    <i data-lucide="underline" class="w-4 h-4"></i>
                                                </button>
                                                <div class="w-px h-6 bg-gray-300"></div>
                                                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded">
                                                    <i data-lucide="list" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded">
                                                    <i data-lucide="link" class="w-4 h-4"></i>
                                                </button>
                                            </div>

                                            <!-- Message Textarea -->
                                            <div class="relative">
                                                <textarea id="mensaje"
                                                    name="mensaje"
                                                    required
                                                    rows="12"
                                                    placeholder="Escribe tu mensaje aquí..."
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none font-mono text-sm leading-relaxed">Estimado cliente,

Adjuntamos las siguientes facturas para su revisión:

<?php
mysqli_data_seek($resultado, 0);
$total_general = 0;
while ($row = mysqli_fetch_assoc($resultado)) {
    $total_general += $row['total'];
    echo sprintf(
        "    • Factura #%s\n      Importe: €%s\n",
        $row['id'],
        number_format($row['total'], 2, ',', '.')
    );
}
echo "\nImporte total: €" . number_format($total_general, 2, ',', '.') . "\n";
?>

Por favor, no dude en contactarnos si tiene alguna pregunta sobre las facturas adjuntas.

Gracias por su atención.

Saludos cordiales,
Tu Empresa</textarea>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden Fields -->
                                    <?php
                                    foreach ($ids_filtrados as $id) {
                                        echo "<input type='hidden' name='facturas[]' value='$id'>";
                                    }
                                    ?>

                                    <!-- Email Actions -->
                                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <button type="button" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                                <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                                Programar envío
                                            </button>
                                            <button type="button" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                                Guardar borrador
                                            </button>
                                        </div>

                                        <div class="flex items-center space-x-3">
                                            <button type="button"
                                                onclick="history.back()"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                                Cancelar
                                            </button>
                                            <button type="submit"
                                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                                                Enviar Correo
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Scripts -->
                        <script>
                            // Initialize Lucide icons
                            lucide.createIcons();

                            // Form validation and submission
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const remitente = document.getElementById('remitente').value;
                                const destinatario = document.getElementById('destinatario').value;
                                const asunto = document.getElementById('asunto').value;
                                const mensaje = document.getElementById('mensaje').value;

                                if (!remitente || !destinatario || !asunto || !mensaje.trim()) {
                                    e.preventDefault();
                                    alert('Por favor, completa todos los campos obligatorios.');
                                    return false;
                                }

                                // Confirmation dialog
                                const facturas = document.querySelectorAll('input[name="facturas[]"]').length;
                                const confirmMsg = `¿Confirmas el envío de ${facturas} factura(s) por correo electrónico?`;

                                if (!confirm(confirmMsg)) {
                                    e.preventDefault();
                                    return false;
                                }
                            });

                            // Auto-resize textarea
                            const textarea = document.getElementById('mensaje');
                            textarea.addEventListener('input', function() {
                                this.style.height = 'auto';
                                this.style.height = this.scrollHeight + 'px';
                            });

                            // Email formatting buttons (basic functionality)
                            document.querySelectorAll('[data-lucide="bold"], [data-lucide="italic"], [data-lucide="underline"]').forEach(button => {
                                button.addEventListener('click', function() {
                                    this.classList.toggle('bg-blue-100');
                                    this.classList.toggle('text-blue-600');
                                });
                            });
                        </script>

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





</body>

</html>