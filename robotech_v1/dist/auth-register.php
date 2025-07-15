<?php
session_start();

include 'conexion.php';

// 2. Comprobar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario  = $_POST['User_Name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $telefono = $_POST['Mobile_Number'];

    // Verificar si el correo ya existe en la base de datos
    $check_email = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        // El correo ya existe, mostrar mensaje de error
        $error_message = "Este correo electrónico ya está registrado. Por favor, utiliza otro.";
        
    } else {
        // 2.1 Insertar usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, telefono) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario, $email, $password, $telefono);
        $stmt->execute();
        $usuario_id = $stmt->insert_id;

        // Guardar en sesión y redirigir
        $_SESSION['usuario'] = $usuario;
        $_SESSION['usuario_id'] = $usuario_id;

        header("Location: auth-login.php");
        exit();
    }
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
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- CSS -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

<div class="relative flex flex-col justify-center min-h-screen overflow-hidden">
    <div class="w-full m-auto bg-white dark:bg-slate-800/60 rounded shadow-lg ring-2 ring-slate-300/50 dark:ring-slate-700/50 lg:max-w-md">
        <div class="text-center p-6 bg-slate-900 rounded-t">
            <a href="index.html"><img src="assets/images/logo-sm.png" alt="" class="w-14 h-14 mx-auto mb-2"></a>
            <h3 class="font-semibold text-white text-xl mb-1">Let's Get Started Tailwind</h3>
            <p class="text-xs text-slate-400">Sign in to continue to Tailwind.</p>
        </div>

        <form class="p-6" method="POST" enctype="multipart/form-data">
            <div>
                <label for="User_Name" class="font-medium text-sm text-slate-600 dark:text-slate-400">Username</label>
                <input type="text" id="User_Name" name="User_Name" required class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70" placeholder="Enter Username">
            </div>
            <div class="mt-2">
                <label for="email" class="font-medium text-sm text-slate-600 dark:text-slate-400">Email</label>
                <input type="email" id="email" name="email" required class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70" placeholder="Enter Email">
            </div>
            <div class="mt-2">
                <label for="password" class="font-medium text-sm text-slate-600 dark:text-slate-400">Your password</label>
                <input type="password" id="password" name="password" required class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70" placeholder="Enter Password">
            </div>
            <div class="mt-2">
                <label for="Confirm_Password" class="font-medium text-sm text-slate-600 dark:text-slate-400">Confirm Password</label>
                <input type="password" id="Confirm_Password" name="Confirm_Password" required class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70" placeholder="Enter Confirm Password">
            </div>
            <div class="mt-2">
                <label for="Mobile_Number" class="font-medium text-sm text-slate-600 dark:text-slate-400">Mobile Number</label>
                <input type="text" id="Mobile_Number" name="Mobile_Number" required class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70" placeholder="Enter Mobile Number">
            </div>
            
            <div class="block mt-3">
                <label class="custom-label block dark:text-slate-300">
                    <input type="checkbox" class="mr-2" required>
                    By registering you agree to the Robotech Terms of Use
                </label>
            </div>
            <div class="mt-4">
                <button type="submit" class="w-full px-2 py-2 tracking-wide text-white transition-colors duration-200 transform bg-brand-500 rounded-md hover:bg-brand-600 focus:outline-none focus:bg-brand-600">
                    Register
                </button>
            </div>
        </form>

        <?php if (isset($error_message)): ?>
            <div class="mt-4 text-red-500 text-center">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <p class="mb-5 text-sm font-medium text-center text-slate-500">
            Already have an account?
            <a href="auth-login.php" class="font-medium text-brand-500 hover:underline">Log in</a>
        </p>
    </div>
</div>

<script src="assets/libs/lucide/umd/lucide.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
