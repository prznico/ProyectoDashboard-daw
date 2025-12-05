<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../config/constants.php';

$response = ['status' => 'error', 'message' => ''];

// Validar que el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    $response['message'] = 'Acceso denegado. Solo administradores pueden agregar recursos.';
    echo json_encode($response);
    exit;
}

// Espera formulario multipart/form-data
$nombre = trim($_POST['nombre'] ?? '');
$autor = trim($_POST['autor'] ?? '');
$departamento = trim($_POST['departamento'] ?? '');
$empresa = trim($_POST['empresa'] ?? '');
$fecha_creacion = trim($_POST['fecha_creacion'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($nombre === '' || $autor === '' || $fecha_creacion === '') {
    $response['message'] = 'Faltan campos requeridos: nombre, autor o fecha_creacion';
    echo json_encode($response);
    exit;
}

// Validar archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'No se recibió archivo válido';
    echo json_encode($response);
    exit;
}

$file = $_FILES['archivo'];

if ($file['size'] > MAX_FILE_SIZE) {
    $response['message'] = 'Archivo demasiado grande';
    echo json_encode($response);
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ALLOWED_EXTENSIONS;

if (!in_array($ext, $allowed)) {
    $response['message'] = 'Extensión no permitida';
    echo json_encode($response);
    exit;
}

// Asegurar directorio
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$storedName = uniqid('res_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
$destination = UPLOAD_DIR . $storedName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $response['message'] = 'Error moviendo archivo al directorio de uploads';
    echo json_encode($response);
    exit;
}

$url_archivo = UPLOAD_URL . $storedName;
$tipo_archivo = $ext;
$tamanio_mb = round($file['size'] / 1024 / 1024, 2);

// Insertar registro en la base
$stmt = $conexion->prepare(
    'INSERT INTO recursos 
    (nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, tamaño_mb, created_at, updated_at, eliminado) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 0)'
);

// CORRECCIÓN IMPORTANTE: deben ser 10 letras en bind_param, todas cadenas -> "ssssssssss"
$stmt->bind_param(
    'ssssssssss',
    $nombre,
    $autor,
    $departamento,
    $empresa,
    $fecha_creacion,
    $descripcion,
    $storedName,
    $tipo_archivo,
    $url_archivo,
    $tamanio_mb
);

$ok = $stmt->execute();

if ($ok) {
    $response['status'] = 'success';
    $response['message'] = 'Recurso agregado correctamente';
    $response['id'] = $conexion->insert_id;
} else {
    @unlink($destination);
    $response['message'] = 'Error al insertar recurso: ' . $stmt->error;
}

$stmt->close();

echo json_encode($response);
?>
