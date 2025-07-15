<?php
include 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];



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


    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center py-4 w-full">
                <div class="w-full">
                    <div class="flex flex-wrap justify-between">
                        <div class="items-center ">
                            <h1 class="font-medium text-3xl block dark:text-slate-100">Perfil</h1>
                            <ol class="list-reset flex text-sm">
                                <li><a href="#" class="text-gray-500 dark:text-slate-400">Robotech</a></li>
                                <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                <li class="text-gray-500 dark:text-slate-400">Pages</li>
                                <li><span class="text-gray-500 dark:text-slate-400 mx-2">/</span></li>
                                <li class="text-primary-500 hover:text-primary-600 dark:text-primary-400">Perfil</li>
                            </ol>
                        </div><!--end /div-->
                        <div class="flex items-center">
                            <div class="today-date leading-5 mt-2 lg:mt-0 form-input w-auto rounded-md border inline-block border-primary-500/60 dark:border-primary-500/60 text-primary-500 bg-transparent px-3 py-1 focus:outline-none focus:ring-0 placeholder:text-slate-400/70 placeholder:font-normal placeholder:text-sm hover:border-primary-400 focus:border-primary-500 dark:focus:border-primary-500  dark:hover:border-slate-700">
                                <input type="text" class="dash_date border-0 focus:border-0 focus:outline-none" readonly required="">
                            </div>
                        </div><!--end /div-->
                    </div><!--end /div-->
                </div><!--end /div-->
            </div><!--end /div-->
        </div><!--end /div-->
    </div><!--end container-->

    <div class="xl:w-full  min-h-[calc(100vh-152px)] relative pb-14">
        <div class="grid md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4 mb-4">
            <div class="sm:col-span-12  md:col-span-12 lg:col-span-12 xl:col-span-12 ">
                <div class="bg-white dark:bg-slate-800 shadow  rounded-md w-full relative">
                    <div class="flex-auto p-0">
                        <img src="assets/images/widgets/bg-p.png" alt="" class="bg-cover bg-center h-48 w-fit rounded-md clip-path-bottom">
                    </div><!--end card-body-->
                    <div class="flex-auto p-4 pt-0">
                        <div class="grid md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4">
                            <div class="sm:col-span-12  md:col-span-12 lg:col-span-6 xl:col-span-6 ">
                                <div class="flex items-center relative -mt-[74px]">
                                    <div class="w-36 h-36 relative">
                                        <img src="assets/images/users/avatar-7.png" alt="" class="rounded-full border-[8px] border-white dark:border-slate-800">
                                        <span class="absolute cursor-pointer w-7 h-7 bg-green-600 rounded-full bottom-4 right-3 flex items-center justify-center border-2 border-white dark:border-slate-800">
                                            <i class="fas fa-camera text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="self-end ml-3">
                                        <h5 class="text-xl md:text-[28px] font-semibold sm:text-white md:text-slate-700 dark:text-gray-300 mb-0 md:mb-2"><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h5>
                                        <p class="block text-xs lg:text-base  font-medium text-slate-500"><?= htmlspecialchars($_SESSION['usuario']['empresa']) ?></p>
                                    </div>
                                </div>
                            </div><!--end col-->


                        </div><!--end inner-grid-->
                    </div><!--end card-body-->
                </div> <!--end card-->
            </div><!--end col-->
        </div><!--end inner-grid-->

        <div class="grid md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4 ">
            <div class="sm:col-span-12  md:col-span-12 lg:col-span-12 xl:col-span-12 ">
                <div class="w-full relative overflow-hidden">
                    <div class="p-0 xl:p-4">
                        <div class="mb-4 border-b border-dashed border-gray-200 dark:border-gray-700 flex flex-wrap justify-start lg:justify-between" data-fc-type="tab">
                            <ul class="flex flex-wrap mb-5 lg:-mb-px" aria-label="Tabs">
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block py-4 px-4 text-sm font-medium text-center text-gray-500 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-gray-100 dark:border-gray-700" id="Groups-tab" data-fc-target="#Groups" type="button" role="tab" aria-controls="Groups" aria-selected="false">Organizaciones</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block py-4 px-4 text-sm font-medium text-center text-gray-500 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-gray-100 dark:border-gray-700" id="Projects-tab" data-fc-target="#Projects" type="button" role="tab" aria-controls="Projects" aria-selected="false">Projectos</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <!-- <button class="inline-block py-4 px-4 text-sm font-medium text-center text-gray-500 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-gray-100 dark:border-gray-700 active" id="Posts-tab" data-fc-target="#Posts"  type="button" role="tab" aria-controls="Posts" aria-selected="false">Post</button> -->
                                </li>
                                <li role="presentation">
                                    <button class="tab-button inline-block py-4 px-4 text-sm font-medium text-center text-gray-500 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-gray-100 dark:border-gray-700" id="Settings-tab" data-fc-target="#Settings" type="button" role="tab" aria-controls="Settings" aria-selected="false">Ajustes</button>
                                </li>
                            </ul>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const tabs = document.querySelectorAll('.tab-button');

                                    tabs.forEach(tab => {
                                        tab.addEventListener('click', function() {
                                            // Remove active class from all tabs
                                            tabs.forEach(t => {
                                                t.classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-400');
                                                t.setAttribute('aria-selected', 'false');
                                            });

                                            // Add active class to clicked tab
                                            this.classList.add('text-blue-600', 'border-blue-600', 'dark:text-blue-400');
                                            this.setAttribute('aria-selected', 'true');
                                        });
                                    });

                                    // Set Organizations tab as active by default
                                    document.getElementById('Groups-tab').click();
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div><!--end col-->
        </div>


        <div id="Groups" role="tabpanel" aria-labelledby="Groups-tab" class="">

            <!-- Mostrar organizaciones asociadas -->
            <?php $editar_id = $_POST['org_id'] ?? null; ?>

            <section class="p-6 bg-white rounded-2xl shadow-md mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        <i class="icofont-users text-blue-600 mr-2"></i>
                        Organizaciones Asociadas
                    </h2>
                    <a href="perfil-crear-org.php" class="w-48 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="icofont-plus"></i> Crear Organización
                    </a>
                </div>
                <div class="space-y-4">
                    <?php foreach ($organizaciones as $org): ?>
                        <div class="p-4 bg-gray-100 rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-medium text-gray-700"><?= htmlspecialchars($org['nombre']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        CIF: <?= htmlspecialchars($org['cif']) ?> |
                                        Email: <?= htmlspecialchars($org['email']) ?>
                                    </p>
                                </div>
                                <div class="flex space-x-4">
                                    <form method="POST">
                                        <input type="hidden" name="org_id" value="<?= $org['id'] ?>">
                                        <button type="submit" name="editar_form" class="text-blue-500 hover:text-blue-700 font-semibold">
                                            <i class="icofont-ui-edit"></i> Editar
                                        </button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta organización?');">
                                        <input type="hidden" name="org_id" value="<?= $org['id'] ?>">
                                        <button type="submit" name="eliminar_org" class="text-red-500 hover:text-red-700 font-semibold">
                                            <i class="icofont-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <?php if (isset($_POST['editar_form']) && $editar_id == $org['id']): ?>
                                <form method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="hidden" name="editar_org" value="1">
                                    <input type="hidden" name="org_id" value="<?= $org['id'] ?>">

                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" name="org-nombre" value="<?= htmlspecialchars($org['nombre']) ?>" class="w-full p-2 border rounded-md" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-700">CIF</label>
                                        <input type="text" name="org-cif" value="<?= htmlspecialchars($org['cif']) ?>" class="w-full p-2 border rounded-md" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Dirección</label>
                                        <input type="text" name="org-direccion" value="<?= htmlspecialchars($org['direccion']) ?>" class="w-full p-2 border rounded-md" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="org-email" value="<?= htmlspecialchars($org['email']) ?>" class="w-full p-2 border rounded-md" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Teléfono</label>
                                        <input type="text" name="org-telefono" value="<?= htmlspecialchars($org['telefono']) ?>" class="w-full p-2 border rounded-md" />
                                    </div>

                                    <div class="col-span-full">
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                            <i class="icofont-check"></i> Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>


<!-- Mostrar series asociadas -->
<?php 
// Get series from all associated organizations
$series = [];
$sql = "SELECT s.*, o.nombre as organizacion_nombre 
        FROM series s
        INNER JOIN org_series os ON s.id = os.serie_id 
        INNER JOIN organizaciones o ON os.organizacion_id = o.id
        INNER JOIN usuario_organizacion uo ON o.id = uo.organizacion_id
        WHERE uo.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $series[] = $row;
}

$editar_serie_id = $_POST['serie_id'] ?? null;
?>

<section class="p-6 bg-white rounded-2xl shadow-md mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">
            <i class="icofont-package text-blue-600 mr-2"></i>
            Series Asociadas
        </h2>
        <a href="perfil-crear-serie.php" class="w-48 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
            <i class="icofont-plus"></i> Crear Serie
        </a>
    </div>
    <div class="space-y-4">
        <?php foreach ($series as $serie): ?>
            <div class="p-4 bg-gray-100 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-lg font-medium text-gray-700"><?= htmlspecialchars($serie['nombre']) ?></p>
                        <p class="text-sm text-gray-500">
                            Organización: <?= htmlspecialchars($serie['organizacion_nombre']) ?> |
                            Prefijo: <?= htmlspecialchars($serie['prefijo']) ?>
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <form method="POST">
                            <input type="hidden" name="serie_id" value="<?= $serie['id'] ?>">
                            <button type="submit" name="editar_serie_form" class="text-blue-500 hover:text-blue-700 font-semibold">
                                <i class="icofont-ui-edit"></i> Editar
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta serie?');">
                            <input type="hidden" name="serie_id" value="<?= $serie['id'] ?>">
                            <button type="submit" name="eliminar_serie" class="text-red-500 hover:text-red-700 font-semibold">
                                <i class="icofont-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (isset($_POST['editar_serie_form']) && $editar_serie_id == $serie['id']): ?>
                    <form method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="editar_serie" value="1">
                        <input type="hidden" name="serie_id" value="<?= $serie['id'] ?>">

                        <div>
                            <label class="text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="serie-nombre" value="<?= htmlspecialchars($serie['nombre']) ?>" class="w-full p-2 border rounded-md" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Prefijo</label>
                            <input type="text" name="serie-prefijo" value="<?= htmlspecialchars($serie['prefijo']) ?>" class="w-full p-2 border rounded-md" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">IVA (%)</label>
                            <input type="number" step="0.01" name="serie-iva" value="<?= htmlspecialchars($serie['iva']) ?>" class="w-full p-2 border rounded-md" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">IRPF (%)</label>
                            <input type="number" step="0.01" name="serie-irpf" value="<?= htmlspecialchars($serie['irpf']) ?>" class="w-full p-2 border rounded-md" />
                        </div>

                        <div class="col-span-full">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="icofont-check"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>




        </div>



        <div id="Projects" role="tabpanel" aria-labelledby="Projects-tab" class="hidden">
            <!-- contenido de proyectos -->
        </div>

        <div id="Settings" role="tabpanel" aria-labelledby="Settings-tab" class="block max-w-4xl mx-auto">
            <div class="space-y-6">
                <!-- Encabezado -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        <i class="icofont-settings text-blue-600 mr-2"></i>
                        Configuración de Cuenta
                    </h2>
                    <p class="text-gray-600">Gestiona tu información personal y seguridad de la cuenta</p>
                </div>

                <form action="procesar_ajustes.php" method="POST" class="space-y-6">
                    <!-- Datos Personales -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="icofont-user text-blue-600 mr-2"></i>
                                Datos Personales
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre completo
                                    </label>
                                    <input type="text"
                                        id="nombre"
                                        name="nombre"
                                        value="<?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Ingresa tu nombre completo"
                                        required>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Correo electrónico
                                    </label>
                                    <input type="email"
                                        id="email"
                                        name="email"
                                        value="<?= htmlspecialchars($_SESSION['usuario']['email']) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="ejemplo@correo.com"
                                        required>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                        Teléfono
                                    </label>
                                    <input type="tel"
                                        id="telefono"
                                        name="telefono"
                                        value="<?= htmlspecialchars($_SESSION['usuario']['telefono']) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="+34 123 456 789">
                                </div>

                                <!-- Empresa -->
                                <div>
                                    <label for="empresa" class="block text-sm font-medium text-gray-700 mb-2">
                                        Empresa
                                    </label>
                                    <input type="text"
                                        id="empresa"
                                        name="empresa"
                                        value="<?= htmlspecialchars($_SESSION['usuario']['empresa']) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Nombre de la empresa">
                                </div>

                                <!-- Dirección -->
                                <div class="md:col-span-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                                        Dirección
                                    </label>
                                    <input type="text"
                                        id="direccion"
                                        name="direccion"
                                        value="<?= htmlspecialchars($_SESSION['usuario']['direccion']) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Calle, número, ciudad, código postal">
                                </div>

                                <!-- Notas -->
                                <div class="md:col-span-2">
                                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">
                                        Notas adicionales
                                    </label>
                                    <textarea id="notas"
                                        name="notas"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
                                        placeholder="Información adicional, preferencias, etc."><?= htmlspecialchars($_SESSION['usuario']['notas']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cambio de Contraseña -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="icofont-key text-red-600 mr-2"></i>
                                Cambio de Contraseña
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Deja estos campos vacíos si no deseas cambiar tu contraseña</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Contraseña Actual -->
                                <div class="md:col-span-2">
                                    <label for="password_actual" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contraseña actual
                                    </label>
                                    <input type="password"
                                        id="password_actual"
                                        name="password_actual"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                        placeholder="Ingresa tu contraseña actual">
                                </div>

                                <!-- Nueva Contraseña -->
                                <div>
                                    <label for="password_nueva" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nueva contraseña
                                    </label>
                                    <input type="password"
                                        id="password_nueva"
                                        name="password_nueva"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                        placeholder="Mínimo 8 caracteres">
                                </div>

                                <!-- Confirmar Nueva Contraseña -->
                                <div>
                                    <label for="password_confirmar" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirmar nueva contraseña
                                    </label>
                                    <input type="password"
                                        id="password_confirmar"
                                        name="password_confirmar"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                        placeholder="Repite la nueva contraseña">
                                </div>
                            </div>

                            <!-- Requisitos de contraseña -->
                            <div class="mt-4 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-700 font-medium mb-2">Requisitos de contraseña:</p>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li class="flex items-center">
                                        <i class="icofont-check-circled text-green-600 mr-2"></i>
                                        Mínimo 8 caracteres
                                    </li>
                                    <li class="flex items-center">
                                        <i class="icofont-check-circled text-green-600 mr-2"></i>
                                        Al menos una letra mayúscula y minúscula
                                    </li>
                                    <li class="flex items-center">
                                        <i class="icofont-check-circled text-green-600 mr-2"></i>
                                        Al menos un número
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row gap-4 justify-end">
                            <button type="button"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="icofont-close mr-2"></i>
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="icofont-save mr-2"></i>
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            // Validación básica del formulario
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const passwordNueva = document.getElementById('password_nueva');
                const passwordConfirmar = document.getElementById('password_confirmar');

                // Validar que las contraseñas coincidan
                function validatePasswords() {
                    if (passwordNueva.value && passwordConfirmar.value) {
                        if (passwordNueva.value !== passwordConfirmar.value) {
                            passwordConfirmar.setCustomValidity('Las contraseñas no coinciden');
                        } else {
                            passwordConfirmar.setCustomValidity('');
                        }
                    }
                }

                passwordNueva.addEventListener('input', validatePasswords);
                passwordConfirmar.addEventListener('input', validatePasswords);

                // Validar formulario antes de enviar
                form.addEventListener('submit', function(e) {
                    const passwordActual = document.getElementById('password_actual').value;
                    const passwordNueva = document.getElementById('password_nueva').value;
                    const passwordConfirmar = document.getElementById('password_confirmar').value;

                    // Si se quiere cambiar la contraseña, todos los campos son obligatorios
                    if (passwordNueva || passwordConfirmar) {
                        if (!passwordActual) {
                            e.preventDefault();
                            alert('Debes ingresar tu contraseña actual para cambiarla');
                            return;
                        }
                        if (!passwordNueva) {
                            e.preventDefault();
                            alert('Debes ingresar una nueva contraseña');
                            return;
                        }
                        if (passwordNueva !== passwordConfirmar) {
                            e.preventDefault();
                            alert('Las contraseñas no coinciden');
                            return;
                        }
                        if (passwordNueva.length < 8) {
                            e.preventDefault();
                            alert('La nueva contraseña debe tener al menos 8 caracteres');
                            return;
                        }
                    }
                });
            });
        </script>



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