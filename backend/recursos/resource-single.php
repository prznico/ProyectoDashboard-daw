<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

$id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'ID inválido']);
    exit;
}

$stmt = $conexion->prepare('SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, tamaño_mb, created_at, updated_at FROM recursos WHERE id = ? AND eliminado = 0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['status'=>'error','message'=>'Recurso no encontrado']);
    exit;
}

foreach ($row as $k => $v) $row[$k] = is_string($v) ? utf8_encode($v) : $v;

echo json_encode($row, JSON_PRETTY_PRINT);

?>
