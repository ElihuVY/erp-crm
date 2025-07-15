<?php
session_start();
include 'conexion.php';


// Validar que se accede mediante POST (es decir, desde el formulario)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // Obtener datos del formulario
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    header("Location: auth-login.html?error=campos");
    exit();
  }

  // Buscar usuario por email
  $sql = "SELECT * FROM usuarios WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($password, $usuario['password'])) {
      // Guardar info del usuario en sesión
      $_SESSION['usuario'] = $usuario['nombre'];
      $_SESSION['usuario_id'] = $usuario['id'];
      $_SESSION['id_nif'] = $usuario['id_nif'] ?? null;

      $_SESSION['usuario'] = $usuario;
      // Buscar organización vinculada
      $org_sql = "SELECT o.id 
                        FROM organizaciones o
                        JOIN usuario_organizacion uo ON o.id = uo.organizacion_id
                        WHERE uo.usuario_id = ?
                        LIMIT 1";
      $org_stmt = $conn->prepare($org_sql);
      $org_stmt->bind_param("i", $_SESSION['usuario_id']);
      $org_stmt->execute();
      $org_result = $org_stmt->get_result();

      if ($org = $org_result->fetch_assoc()) {
        $_SESSION['organizacion_id'] = $org['id'];
      } else {
        $_SESSION['organizacion_id'] = null;
      }

      // Redirigir al dashboard o inicio
      header("Location: inicio.php");
      exit();
    } else {
      header("Location: auth-login.php?error=contrasena");
      exit();
    }
  } else {
    header("Location: auth-login.php?error=usuario");
    exit();
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
  <meta charset="utf-8" />
  <title>Robotech - Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="shortcut icon" href="assets/images/favicon.ico" />
  <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
  <link href="assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>

<body class="bg-[#EEF0FC] dark:bg-gray-900">

  <div class="relative flex flex-col justify-center min-h-screen overflow-hidden">
    <div class="w-full m-auto bg-white dark:bg-slate-800/60 rounded shadow-lg ring-2 ring-slate-300/50 dark:ring-slate-700/50 lg:max-w-md">
      <div class="text-center p-6 bg-slate-900 rounded-t">
        <a href="index.php"><img src="assets/images/logo-sm.png" alt="" class="w-14 h-14 mx-auto mb-2"></a>
        <h3 class="font-semibold text-white text-xl mb-1">Bienvenido de nuevo!</h3>
        <p class="text-xs text-slate-400">Sign in to your Robotech account</p>
      </div>

      <!-- 🔔 ALERTAS -->
      <div id="alertas" class="p-4 space-y-2"></div>

      <!-- ✅ FORMULARIO DE LOGIN -->
      <form class="p-6" method="POST">
        <div>
          <label for="email" class="font-medium text-sm text-slate-600 dark:text-slate-400">Email</label>
          <input type="email" id="email" name="email" required
            class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70"
            placeholder="Your Email">
        </div>
        <div class="mt-4">
          <label for="password" class="font-medium text-sm text-slate-600 dark:text-slate-400">Password</label>
          <input type="password" id="password" name="password" required
            class="form-input w-full rounded-md mt-1 border border-slate-300/60 dark:border-slate-700 dark:text-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70"
            placeholder="Enter Password">
        </div>
        <a href="#" class="text-xs font-medium text-brand-500 underline">Forgot Password?</a>
        <div class="block mt-3">
          <label class="custom-label block dark:text-slate-300">
            <input type="checkbox" class="mr-2"> Remember me
          </label>
        </div>
        <div class="mt-4">
          <button type="submit"
            class="w-full px-2 py-2 tracking-wide text-white transition-colors duration-200 transform bg-brand-500 rounded hover:bg-brand-600 focus:outline-none focus:bg-brand-600">
            Login
          </button>
        </div>
      </form>

      <p class="mb-5 text-sm font-medium text-center text-slate-500">
        Don't have an account? <a href="auth-register.php"
          class="font-medium text-brand-500 hover:underline">Sign up</a>
      </p>
    </div>
  </div>

  <!-- 📜 JAVASCRIPTS -->
  <script src="assets/libs/lucide/umd/lucide.min.js"></script>
  <script src="assets/libs/simplebar/simplebar.min.js"></script>
  <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
  <script src="assets/js/app.js"></script>

  <script>
    const params = new URLSearchParams(window.location.search);
    const alertas = document.getElementById("alertas");

    if (params.get("error") === "usuario") {
      mostrarAlerta("❌ Usuario no encontrado.", "red");
    } else if (params.get("error") === "contrasena") {
      mostrarAlerta("❌ Contraseña incorrecta.", "red");
    } else if (params.get("registro") === "ok") {
      mostrarAlerta("✅ Usuario registrado con éxito. Puedes iniciar sesión.", "green");
    }

    function mostrarAlerta(mensaje, color) {
      const div = document.createElement("div");
      div.className = `bg-${color}-100 text-${color}-800 border border-${color}-300 px-4 py-2 rounded text-sm text-center`;
      div.innerText = mensaje;
      alertas.appendChild(div);
    }
  </script>

</body>

</html>