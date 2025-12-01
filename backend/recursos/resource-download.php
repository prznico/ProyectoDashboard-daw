<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../config/constants.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'ID inválido']);
    exit;
}

$stmt = $conexion->prepare('SELECT nombre_archivo, url_archivo FROM recursos WHERE id = ? AND eliminado = 0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Recurso no encontrado']);
    exit;
}

$storedName = $row['nombre_archivo'];
$filePath = UPLOAD_DIR . $storedName;
if (!file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Archivo no encontrado en servidor']);
    exit;
}

// Registrar descarga en bitacora_descargas (si tenemos session, registrar usuario)
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$stmt = $conexion->prepare('INSERT INTO bitacora_descargas (usuario_id, recurso_id, fecha_descarga, ip_address) VALUES (?, ?, NOW(), ?)');
$stmt->bind_param('iis', $usuario_id, $id, $ip);
$stmt->execute();
$stmt->close();

// Enviar headers y contenido para descarga
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
finfo_close($finfo);

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

?>
