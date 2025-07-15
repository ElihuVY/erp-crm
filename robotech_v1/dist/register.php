<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "erp-crm";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del usuario
    $usuario  = $_POST['User_Name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $telefono = $_POST['Mobile_Number'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $usuario, $email, $password, $telefono);
    $stmt->execute();
    $usuario_id = $stmt->insert_id;

    // Datos de la organización
    $org_nombre    = $_POST['org_nombre'];
    $org_cif       = $_POST['org_cif'];
    $org_direccion = $_POST['org_direccion'];

    // Subida de logo con ruta
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = uniqid('logo_') . '_' . basename($_FILES['logo']['name']);
        $ruta_destino = "uploads/" . $nombre_archivo;

        // Guardar físicamente en la carpeta /uploads/
        if (move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . "/$ruta_destino")) {
            $logo_path = $ruta_destino; // Esta es la ruta que se guarda en la BD
        }
    }

    // Insertar la organización con el path del logo
    $stmt_org = $conn->prepare("INSERT INTO organizaciones (nombre, cif, direccion, logo) VALUES (?, ?, ?, ?)");
    $stmt_org->bind_param("ssss", $org_nombre, $org_cif, $org_direccion, $logo_path);
    $stmt_org->execute();
    $organizacion_id = $stmt_org->insert_id;

    // Relación entre usuario y organización
    $stmt_link = $conn->prepare("INSERT INTO usuario_organizacion (usuario_id, organizacion_id) VALUES (?, ?)");
    $stmt_link->bind_param("ii", $usuario_id, $organizacion_id);
    $stmt_link->execute();

    // Guardar en sesión
    $_SESSION['usuario'] = $usuario;
    $_SESSION['organizacion_id'] = $organizacion_id;

    // Redirigir al dashboard
    header("Location: index.html");
    exit();
}
?>
