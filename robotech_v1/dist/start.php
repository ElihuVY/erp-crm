<?php
// Ruta del archivo de configuración que indica que ya se ejecutó el asistente
$installMarker = __DIR__ . '/config/install-completed.txt';

// Si ya se instaló, redirigir al login
if (file_exists($installMarker)) {
    header('Location: auth-login.php');
    exit();
}

// Si aún no se ha instalado, redirigir al asistente
header('Location: asistente.php');
exit();
