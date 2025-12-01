<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'status' => 'success',
        'usuario' => [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? null,
            'rol' => $_SESSION['usuario_rol'] ?? null
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Sin sesión activa']);
}

?>
