<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';
session_start();

// Leer input JSON o POST
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Credenciales incompletas']);
    exit;
}

$stmt = $conexion->prepare('SELECT id, nombre, email, contraseña, rol, activo FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    exit;
}

if (!$user['activo']) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario desactivado']);
    exit;
}

if (!password_verify($password, $user['contraseña'])) {
    echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
    exit;
}

// Login exitoso: crear sesión
$_SESSION['usuario_id'] = $user['id'];
$_SESSION['usuario_nombre'] = $user['nombre'];
$_SESSION['usuario_rol'] = $user['rol'];

// Registrar acceso (login) en bitacora_acceso
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$stmt = $conexion->prepare('INSERT INTO bitacora_acceso (usuario_id, tipo_acceso, recurso_id, fecha_hora, ip_address) VALUES (?, ?, NULL, NOW(), ?)');
$tipo = 'login';
$stmt->bind_param('iss', $user['id'], $tipo, $ip);
$stmt->execute();
$stmt->close();

// Retornar datos útiles sin la contraseña
unset($user['contraseña']);
echo json_encode(['status' => 'success', 'message' => 'Login correcto', 'user' => $user]);

?>
