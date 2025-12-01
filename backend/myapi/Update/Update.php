<?php
    namespace MyAPI\Update;
    require_once __DIR__ . '/../DataBase.php';
    use MyAPI\DataBase as DataBase;

    class Update extends DataBase{
        public function __construct($db) {
            parent::__construct($db);
        }
        public function edit($producto){
            $data = array(
                'status'  => 'error',
                'message' => 'No se pudo actualizar el recurso'
            );

            $sql = "UPDATE recursos SET nombre = ?, autor = ?, departamento = ?, empresa_institucion = ?, fecha_creacion = ?, descripcion = ?, nombre_archivo = ?, tipo_archivo = ?, url_archivo = ?, `tamaño_mb` = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $nombre = $producto->nombre;
                $autor = $producto->autor ?? '';
                $departamento = $producto->departamento ?? '';
                $empresa = $producto->empresa_institucion ?? ($producto->empresa ?? '');
                $fecha_creacion = $producto->fecha_creacion ?? null;
                $descripcion = $producto->descripcion ?? '';
                $nombre_archivo = $producto->nombre_archivo ?? ($producto->storedName ?? '');
                $tipo_archivo = $producto->tipo_archivo ?? '';
                $url_archivo = $producto->url_archivo ?? '';
                $tamano_mb = isset($producto->tamanio_mb) ? floatval($producto->tamanio_mb) : (isset($producto->tamaño_mb) ? floatval($producto->{'tamaño_mb'}) : 0.0);
                $id = intval($producto->id);

                // tipos: 9 strings then double then int -> 'ssssssssdi'
                $bind = $stmt->bind_param('ssssssssdi', $nombre, $autor, $departamento, $empresa, $fecha_creacion, $descripcion, $nombre_archivo, $tipo_archivo, $url_archivo, $tamano_mb, $id);
                if ($bind === false) {
                    $data['message'] = 'Error bind_param: ' . $stmt->error;
                } else {
                    if ($stmt->execute()) {
                        $data['status'] =  "success";
                        $data['message'] =  "Recurso editado correctamente";
                    } else {
                        $data['message'] = "ERROR: No se ejecutó la consulta. " . $stmt->error;
                    }
                }
                $stmt->close();
            } else {
                $data['message'] = 'Error prepare: ' . $this->conexion->error;
            }

            // Cierra la conexion
            $this->conexion->close();

            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }
    }
?>