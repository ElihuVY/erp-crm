<?php
include 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];

// CREAR ORGANIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_org'])) {
    $nombre = $_POST['org-name'] ?? '';
    $cif = $_POST['org-cif'] ?? '';
    $direccion = $_POST['org-direccion'] ?? '';
    $logo = ''; // Puedes implementar subida luego
    $email = $_POST['org-email'] ?? '';
    $telefono = $_POST['org-telefono'] ?? '';

    if ($nombre !== '') {
        $stmt = $conn->prepare("INSERT INTO organizaciones (nombre, cif, direccion, logo, email, telefono) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $cif, $direccion, $logo, $email, $telefono);
        $stmt->execute();
        $org_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO usuario_organizacion (usuario_id, organizacion_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $usuario_id, $org_id);
        $stmt2->execute();
    }
    header("Location: vista-perfil.php");
    exit;
}

// ELIMINAR ORGANIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_org'])) {
    $org_id = $_POST['org_id'];

    // Verifica si está asociada al usuario
    $stmt = $conn->prepare("SELECT * FROM usuario_organizacion WHERE usuario_id = ? AND organizacion_id = ?");
    $stmt->bind_param("ii", $usuario_id, $org_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eliminar relación
        $stmtDel = $conn->prepare("DELETE FROM usuario_organizacion WHERE usuario_id = ? AND organizacion_id = ?");
        $stmtDel->bind_param("ii", $usuario_id, $org_id);
        $stmtDel->execute();

        // Si nadie más la tiene, la borramos
        $stmtCheck = $conn->prepare("SELECT COUNT(*) as total FROM usuario_organizacion WHERE organizacion_id = ?");
        $stmtCheck->bind_param("i", $org_id);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result()->fetch_assoc();

        if ($res['total'] == 0) {
            $stmt = $conn->prepare("DELETE FROM organizaciones WHERE id = ?");
            $stmt->bind_param("i", $org_id);
            $stmt->execute();
        }
    }
    header("Location: vista-perfil.php");
    exit;
}
// EDITAR ORGANIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_org'])) {
    $org_id = $_POST['org_id'];
    $nombre = $_POST['org-nombre'] ?? '';
    $cif = $_POST['org-cif'] ?? '';
    $direccion = $_POST['org-direccion'] ?? '';
    $email = $_POST['org-email'] ?? '';
    $telefono = $_POST['org-telefono'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM usuario_organizacion WHERE usuario_id = ? AND organizacion_id = ?");
    $stmt->bind_param("ii", $usuario_id, $org_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmtUpd = $conn->prepare("UPDATE organizaciones SET nombre = ?, cif = ?, direccion = ?, email = ?, telefono = ? WHERE id = ?");
        $stmtUpd->bind_param("sssssi", $nombre, $cif, $direccion, $email, $telefono, $org_id);
        $stmtUpd->execute();
    }
    header("Location: vista-perfil.php");
    exit;
}


// LISTAR ORGANIZACIONES
$organizaciones = [];
$sql = "SELECT o.* FROM organizaciones o
        INNER JOIN usuario_organizacion uo ON o.id = uo.organizacion_id
        WHERE uo.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $organizaciones[] = $row;
}


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
    <link href="assets/libs/prismjs/themes/prism-twilight.min.css" type="text/css" rel="stylesheet">
    <!-- Main Css -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <style>
        /* styles.css */
        section {
            padding-right: 1rem;
        }
    </style>
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <!-- leftbar-tab-menu -->


    <?php
    include 'menu.php';
    ?>




        <div id="Groups" role="tabpanel" aria-labelledby="Groups-tab" class="">
            <div class="grid grid-cols-1 gap-y-8 p-8">
                <!-- Formulario para crear nueva organización -->
                <section class="p-6 bg-white rounded-2xl shadow-lg h-full pr-section-org max-w-2xl mx-auto w-full">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        <i class="icofont-plus-circle text-green-600 mr-2"></i>
                        Crear Nueva Organización
                    </h2>
                    <form method="POST" action="vista-perfil.php" class="space-y-4">
                        <input type="hidden" name="crear_org" value="1">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="org-name" required
                                class="w-full mt-1 p-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                placeholder="Nombre de la organización" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">CIF</label>
                            <input type="text" name="org-cif"
                                class="w-full mt-1 p-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                placeholder="A12345678" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" name="org-direccion"
                                class="w-full mt-1 p-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                placeholder="Dirección completa" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="org-email"
                                class="w-full mt-1 p-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                placeholder="contacto@empresa.com" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" name="org-telefono"
                                class="w-full mt-1 p-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                placeholder="912345678" />
                        </div>
                        <div class="flex gap-4">
                            <a href="vista-perfil.php" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-center">
                                <i class="icofont-close mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                                <i class="icofont-check mr-2"></i>
                                Crear Organización
                            </button>

                        </div>
                        
                    </form>
                </section>
            </div>
        </div>
        <script>
            // Función para cargar organizaciones dinámicamente
            function cargarOrganizaciones() {
                // Aquí iría la lógica para cargar las organizaciones desde la base de datos
                // Por ahora usamos datos de ejemplo
                console.log('Cargando organizaciones...');
            }

            // Validación del formulario de series
            document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
                const organizacionId = document.querySelector('select[name="organizacion_id"]').value;
                const nombre = document.querySelector('input[name="nombre"]').value;

                if (!organizacionId) {
                    e.preventDefault();
                    alert('Por favor, selecciona una organización');
                    return;
                }

                if (!nombre.trim()) {
                    e.preventDefault();
                    alert('Por favor, ingresa un nombre para la serie');
                    return;
                }
            });

            // Cargar organizaciones al cargar la página
            document.addEventListener('DOMContentLoaded', cargarOrganizaciones);
        </script>


        <div id="Projects" role="tabpanel" aria-labelledby="Projects-tab" class="hidden">
            <!-- contenido de proyectos -->
        </div>

        
        



    </div><!--end page-wrapper-->
    </div><!--end /div-->


    <!-- JAVASCRIPTS -->
    <!-- <div class="menu-overlay"></div> -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

    <script src="assets/libs/prismjs/prism.js"></script>
    <script src="assets/js/app.js"></script>
    <!-- JAVASCRIPTS -->
</body>
<!-- Flatpickr -->
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>


</html>