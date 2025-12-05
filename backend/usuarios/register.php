<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

// Leer input JSON o POST
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$nombre = trim($input['nombre'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$rol = $_POST['rol'];

if ($nombre === '' || $email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Email inválido']);
    exit;
}

// Verificar si ya existe
$stmt = $conexion->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'El email ya está registrado']);
    $stmt->close();
    exit;
}
$stmt->close();

// Hash de contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $conexion->prepare('INSERT INTO usuarios (nombre, email, contraseña, rol, fecha_registro, activo) VALUES (?, ?, ?, ?, NOW(), 1)');
$stmt->bind_param('ssss', $nombre, $email, $password_hash, $rol);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar usuario']);
}

?>
