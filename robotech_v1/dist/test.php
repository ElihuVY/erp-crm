<?php
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: auth-login.html");
    exit();
}

$nombre = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center">
        <!-- 👤 Nombre del usuario -->
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-8">
            👋 ¡Hola, <?php echo htmlspecialchars($nombre); ?>!
        </h1>

        <!-- 🔘 Botones -->
        <div class="flex flex-col gap-4">
            <a href="editar-usuario.php" class="bg-blue-600 hover:bg-blue-700 text-white py-3 rounded text-lg font-medium transition">
                ✏️ Editar datos
            </a>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white py-3 rounded text-lg font-medium transition">
                🚪 Cerrar sesión
            </a>
        </div>
    </div>

</body>
</html>
