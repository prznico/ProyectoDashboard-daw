<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

/**
 * Endpoint para obtener estadísticas de descargas por hora del día
 * Retorna JSON con array de objetos { hora: 0-23, cantidad: 5, ... }
 * Útil para gráficas tipo line en Chart.js
 */

$data = [];

// Inicializar array con 24 horas en 0
for ($i = 0; $i < 24; $i++) {
    $data[] = [
        'hora' => sprintf('%02d:00', $i),
        'hora_num' => $i,
        'cantidad' => 0
    ];
}

// Obtener descargas agrupadas por hora del día (últimos 7 días)
$sql = "
    SELECT 
        HOUR(bd.fecha_descarga) as hora,
        COUNT(bd.id) as cantidad
    FROM bitacora_descargas bd
    WHERE bd.fecha_descarga >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY HOUR(bd.fecha_descarga)
    ORDER BY HOUR(bd.fecha_descarga) ASC
";

if ($result = $conexion->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $hora_num = intval($row['hora']);
        if (isset($data[$hora_num])) {
            $data[$hora_num]['cantidad'] = intval($row['cantidad']);
        }
    }
    $result->free();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Query Error: ' . mysqli_error($conexion)]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'period' => 'Últimos 7 días',
    'data' => $data,
    'total_descargas' => array_sum(array_column($data, 'cantidad'))
], JSON_PRETTY_PRINT);

?>
