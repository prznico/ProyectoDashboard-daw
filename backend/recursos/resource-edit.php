<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../config/constants.php';

$response = ['status'=>'error','message'=>''];

// Validar que el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    $response['message'] = 'Acceso denegado. Solo administradores pueden editar recursos.';
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    $response['message'] = 'ID inválido';
    echo json_encode($response);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$autor = trim($_POST['autor'] ?? '');
$departamento = trim($_POST['departamento'] ?? '');
$empresa = trim($_POST['empresa'] ?? '');
$fecha_creacion = trim($_POST['fecha_creacion'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

// Validar mínimos
if ($nombre === '' || $autor === '' || $fecha_creacion === '') {
    $response['message'] = 'Faltan campos requeridos';
    echo json_encode($response);
    exit;
}

// Obtener registro actual
$stmt = $conexion->prepare('SELECT nombre_archivo FROM recursos WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$current = $res->fetch_assoc();
$stmt->close();

$update_file = false;
$storedName = $current['nombre_archivo'] ?? null;

// Si viene archivo nuevo
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['archivo'];
    if ($file['size'] > MAX_FILE_SIZE) {
        $response['message'] = 'Archivo demasiado grande';
        echo json_encode($response);
        exit;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $response['message'] = 'Extensión no permitida';
        echo json_encode($response);
        exit;
    }

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $newName = uniqid('res_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $destination = UPLOAD_DIR . $newName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $response['message'] = 'No se pudo guardar el archivo';
        echo json_encode($response);
        exit;
    }
    // borrar antiguo si existe
    if ($storedName && file_exists(UPLOAD_DIR . $storedName)) {
        @unlink(UPLOAD_DIR . $storedName);
    }
    $storedName = $newName;
    $url_archivo = UPLOAD_URL . $storedName;
    $tipo_archivo = $ext;
    $tamanio_mb = round($file['size'] / 1024 / 1024, 2);
    $update_file = true;
}

if ($update_file) {
    $stmt = $conexion->prepare('UPDATE recursos SET nombre = ?, autor = ?, departamento = ?, empresa_institucion = ?, fecha_creacion = ?, descripcion = ?, nombre_archivo = ?, tipo_archivo = ?, url_archivo = ?, tamaño_mb = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('ssssssssdii', $nombre, $autor, $departamento, $empresa, $fecha_creacion, $descripcion, $storedName, $tipo_archivo, $url_archivo, $tamanio_mb, $id);
} else {
    $stmt = $conexion->prepare('UPDATE recursos SET nombre = ?, autor = ?, departamento = ?, empresa_institucion = ?, fecha_creacion = ?, descripcion = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('ssssssi', $nombre, $autor, $departamento, $empresa, $fecha_creacion, $descripcion, $id);
}

$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
    $response['status'] = 'success';
    $response['message'] = 'Recurso actualizado';
} else {
    $response['message'] = 'Error actualizando: ' . $err;
}

echo json_encode($response);

?>
