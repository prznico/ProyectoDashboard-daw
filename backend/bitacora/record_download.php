<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';
session_start();

/**
 * Endpoint para registrar una descarga en la bitácora
 * Espera: POST con { recurso_id: int }
 * Responde: { status: 'success|error', message: '...' }
 */

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$recurso_id = intval($input['recurso_id'] ?? 0);
if ($recurso_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de recurso inválido']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Insertar registro en bitacora_descargas
$stmt = $conexion->prepare('INSERT INTO bitacora_descargas (usuario_id, recurso_id, fecha_descarga, ip_address) VALUES (?, ?, NOW(), ?)');
if ($stmt) {
    $usuario_val = is_null($usuario_id) ? null : intval($usuario_id);
    $stmt->bind_param('iis', $usuario_val, $recurso_id, $ip);
    $ok = $stmt->execute();
    $stmt->close();
    
    if ($ok) {
        echo json_encode(['status' => 'success', 'message' => 'Descarga registrada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar descarga: ' . $conexion->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error prepare: ' . $conexion->error]);
}

?>
