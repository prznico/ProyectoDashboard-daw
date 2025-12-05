<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../database.php';

// Validar que el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status'=>'error','message'=>'Acceso denegado. Solo administradores pueden eliminar recursos.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'ID inválido']);
    exit;
}

$stmt = $conexion->prepare('UPDATE recursos SET eliminado = 1, updated_at = NOW() WHERE id = ?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
    echo json_encode(['status'=>'success','message'=>'Recurso eliminado (marcado)']);
} else {
    echo json_encode(['status'=>'error','message'=>'Error al eliminar: ' . $err]);
}

?>
