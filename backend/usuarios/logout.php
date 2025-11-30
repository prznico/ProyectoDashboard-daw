<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;

// Registrar logout en bitacora
if ($usuario_id) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $conexion->prepare('INSERT INTO bitacora_acceso (usuario_id, tipo_acceso, recurso_id, fecha_hora, ip_address) VALUES (?, ?, NULL, NOW(), ?)');
    $tipo = 'logout';
    $stmt->bind_param('iss', $usuario_id, $tipo, $ip);
    $stmt->execute();
    $stmt->close();
}

// Destruir sesión
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

echo json_encode(['status' => 'success', 'message' => 'Sesión cerrada']);

?>
