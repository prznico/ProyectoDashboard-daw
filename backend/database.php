<?php
    $conexion = @mysqli_connect(
        'localhost',
        'root',
        'N1n1c0l3.',
        'dashboard_recursos'
    );

    // Configurar charset UTF-8
    mysqli_set_charset($conexion, "utf8mb4");

    /**
     * NOTA: si la conexión falló $conexion contendrá false
     **/
    if(!$conexion) {
        die('¡Base de datos NO conectada!');
    }
?>