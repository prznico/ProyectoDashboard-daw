<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

$data = [];
$sql = "SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, tamaño_mb, created_at, updated_at FROM recursos WHERE eliminado = 0 ORDER BY created_at DESC";
if ($result = $conexion->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        // asegurar utf8
        foreach ($row as $k => $v) $row[$k] = is_string($v) ? utf8_encode($v) : $v;
        $data[] = $row;
    }
    $result->free();
}

echo json_encode($data, JSON_PRETTY_PRINT);

?>
