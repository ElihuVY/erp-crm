<?php
include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    die('Acceso denegado.');
}

$nombre    = $_POST['nombre'] ?? '';
$email     = $_POST['email'] ?? '';
$telefono  = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$empresa   = $_POST['empresa'] ?? '';
$notas     = $_POST['notas'] ?? '';

$password_actual        = $_POST['password_actual'] ?? '';
$nueva_password         = $_POST['nueva_password'] ?? '';
$nueva_password_confirm = $_POST['nueva_password_confirm'] ?? '';

// Actualizar datos personales
$stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, direccion = ?, empresa = ?, notas = ? WHERE id = ?");
$stmt->bind_param("ssssssi", $nombre, $email, $telefono, $direccion, $empresa, $notas, $usuario_id);
if (!$stmt->execute()) {
    die('Error al actualizar datos personales: ' . $stmt->error);
}
$stmt->close();

// Cambiar contraseña si corresponde
if (!empty($password_actual) && !empty($nueva_password)) {
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($password_hash_bd);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($password_actual, $password_hash_bd)) {
        die('❌ La contraseña actual no es correcta.');
    }

    if ($nueva_password !== $nueva_password_confirm) {
        die('❌ La nueva contraseña no coincide con la confirmación.');
    }

    $nueva_password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_password_hash, $usuario_id);
    if (!$stmt->execute()) {
        die('Error al actualizar la contraseña: ' . $stmt->error);
    }
    $stmt->close();
}

// Recargar datos del usuario y actualizar sesión
$stmt = $conn->prepare("SELECT id, id_nif, nombre, email, telefono, direccion, empresa, notas FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario_actualizado = $result->fetch_assoc();
$stmt->close();

if ($usuario_actualizado) {
    $_SESSION['usuario'] = $usuario_actualizado;
}

// Redirección
header("Location: vista-perfil.php");
exit();
