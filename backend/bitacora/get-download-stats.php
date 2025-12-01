<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

/**
 * Endpoint para obtener estadísticas de descargas por tipo de archivo
 * Retorna JSON con array de objetos { tipo_archivo: 'pdf', cantidad: 5, ... }
 * Útil para gráficas tipo pie/doughnut en Chart.js
 */

$data = [];

// Obtener descargas agrupadas por tipo de archivo
$sql = "
    SELECT 
        r.tipo_archivo,
        COUNT(bd.id) as cantidad,
        SUM(CASE WHEN bd.usuario_id IS NOT NULL THEN 1 ELSE 0 END) as descargas_autenticadas
    FROM bitacora_descargas bd
    INNER JOIN recursos r ON bd.recurso_id = r.id
    WHERE bd.fecha_descarga >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY r.tipo_archivo
    ORDER BY cantidad DESC
";

if ($result = $conexion->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'tipo_archivo' => $row['tipo_archivo'] ?? 'desconocido',
            'cantidad' => intval($row['cantidad']),
            'descargas_autenticadas' => intval($row['descargas_autenticadas']),
            'descargas_anonimas' => intval($row['cantidad']) - intval($row['descargas_autenticadas'])
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
    'total_descargas' => array_sum(array_column($data, 'cantidad'))
], JSON_PRETTY_PRINT);

?>
