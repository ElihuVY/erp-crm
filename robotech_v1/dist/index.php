<?php
// Define la ruta del archivo que indica si ya se configuró la BBDD
$archivoInstalado = __DIR__ . '/config/install-completed.txt';

// Si el archivo NO existe, mostramos asistente.php
if (!file_exists($archivoInstalado)) {
    include 'asistente.php';
    exit;
} else {
    // Ya instalado, mostramos login
    include 'auth-login.php';
    exit;
}
