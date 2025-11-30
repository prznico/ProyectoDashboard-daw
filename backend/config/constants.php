<?php
// CONFIGURACIÓN PARA EL CONTROL DE LOS ARCHIVOS DE RECURSOS
// Rutas
define('PROJECT_URL', 'http://localhost/proyecto_daw/');
define('UPLOAD_DIR', __DIR__ . '/../../uploads/recursos/');
define('UPLOAD_URL', 'uploads/recursos/');

// Límites de archivo
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'txt', 'jpg', 'png', 'gif']);

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_VISITANTE', 'visitante');

// Mensajes de respuesta
define('MSG_SUCCESS', 'Operación exitosa');
define('MSG_ERROR', 'Error en la operación');
define('MSG_SESSION_EXPIRED', 'Tu sesión ha expirado');
define('MSG_UNAUTHORIZED', 'No tienes permisos para esta acción');

// Íconos por tipo de archivo
$GLOBALS['FILE_ICONS'] = [
    'pdf' => '📄',
    'doc' => '📝',
    'docx' => '📝',
    'xls' => '📊',
    'xlsx' => '📊',
    'ppt' => '🎬',
    'pptx' => '🎬',
    'zip' => '📦',
    'rar' => '📦',
    'txt' => '📋',
    'jpg' => '🖼️',
    'png' => '🖼️',
    'gif' => '🖼️'
];
?>
<?php
// CONFIGURACIÓN PARA EL CONTROL DE LOS ARCHIVOS DE RECURSOS

// URL base del proyecto (ajusta si tu carpeta no es 'proyecto_daw')
define('PROJECT_URL', 'http://localhost/proyecto_daw/');

// Rutas de upload (ruta de sistema y URL pública relativa)
define('UPLOAD_DIR', __DIR__ . '/../../uploads/recursos/');
define('UPLOAD_URL', 'uploads/recursos/');

// Límites de archivo
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'txt', 'jpg', 'png', 'gif']);

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_VISITANTE', 'visitante');

// Mensajes comunes
define('MSG_SUCCESS', 'Operación exitosa');
define('MSG_ERROR', 'Error en la operación');
define('MSG_SESSION_EXPIRED', 'Tu sesión ha expirado');
define('MSG_UNAUTHORIZED', 'No tienes permisos para esta acción');

// Íconos simples por tipo de archivo (puedes cambiar por rutas de imágenes)
$GLOBALS['FILE_ICONS'] = [
    'pdf' => '📄',
    'doc' => '📝',
    'docx' => '📝',
    'xls' => '📊',
    'xlsx' => '📊',
    'ppt' => '🎬',
    'pptx' => '🎬',
    'zip' => '📦',
    'rar' => '📦',
    'txt' => '📋',
    'jpg' => '🖼️',
    'png' => '🖼️',
    'gif' => '🖼️'
];
?>