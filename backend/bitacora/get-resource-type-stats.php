<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

/**
 * Endpoint para obtener estadísticas de recursos por tipo
 * Retorna JSON con array de objetos { tipo_archivo: 'pdf', cantidad_recursos: 5, descargas: 10, ... }
 * Útil para entender qué tipos de archivos son más solicitados
 */

$data = [];

// Obtener conteo de recursos y descargas agrupadas por tipo de archivo
$sql = "
    SELECT 
        r.tipo_archivo,
        COUNT(DISTINCT r.id) as cantidad_recursos,
        COUNT(bd.id) as cantidad_descargas,
        AVG(bd.id IS NOT NULL) * 100 as tasa_descarga
    FROM recursos r
    LEFT JOIN bitacora_descargas bd ON r.id = bd.recurso_id AND bd.fecha_descarga >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    WHERE r.eliminado = 0
    GROUP BY r.tipo_archivo
    ORDER BY cantidad_descargas DESC
";

if ($result = $conexion->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'tipo_archivo' => $row['tipo_archivo'] ?? 'desconocido',
            'cantidad_recursos' => intval($row['cantidad_recursos']),
            'cantidad_descargas' => intval($row['cantidad_descargas']),
            'tasa_descarga' => round($row['tasa_descarga'], 2)
        ];
    }
    $result->free();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Query Error: ' . mysqli_error($conexion)]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'period' => 'Últimos 30 días',
    'data' => $data,
    'total_recursos' => array_sum(array_column($data, 'cantidad_recursos')),
    'total_descargas' => array_sum(array_column($data, 'cantidad_descargas'))
], JSON_PRETTY_PRINT);

?>
