<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

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
