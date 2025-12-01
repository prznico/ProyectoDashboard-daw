<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database.php';

/**
 * Endpoint para obtener estadísticas de descargas por día de la semana
 * Retorna JSON con array de objetos { dia: 'Monday', cantidad: 5, ... }
 * Útil para gráficas tipo bar en Chart.js
 */

$data = [];
$dias = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 
         'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];

// Obtener descargas agrupadas por día de la semana (últimos 30 días)
$sql = "
    SELECT 
        DAYNAME(bd.fecha_descarga) as dia_semana,
        COUNT(bd.id) as cantidad
    FROM bitacora_descargas bd
    WHERE bd.fecha_descarga >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DAYOFWEEK(bd.fecha_descarga), DAYNAME(bd.fecha_descarga)
    ORDER BY DAYOFWEEK(bd.fecha_descarga) ASC
";

if ($result = $conexion->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $dia_en = $row['dia_semana'] ?? 'Unknown';
        $dia_es = $dias[$dia_en] ?? $dia_en;
        $data[] = [
            'dia_en' => $dia_en,
            'dia_es' => $dia_es,
            'cantidad' => intval($row['cantidad'])
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
