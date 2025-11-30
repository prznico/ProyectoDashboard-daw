<?php
namespace MyAPI\Create;
require_once __DIR__ . '/../DataBase.php';
use MyAPI\DataBase as DataBase;

class Create extends DataBase{
    public function __construct($db) {
        parent::__construct($db);
    }
    public function add($recurso){
        $data = array(
            'status'  => 'error',
            'message' => 'Ya existe un recurso con ese nombre'
        );

        // Comprobar existencia por nombre
        $sqlCheck = "SELECT id FROM recursos WHERE nombre = ? AND eliminado = 0 LIMIT 1";
        $stmt = $this->conexion->prepare($sqlCheck);
        if ($stmt) {
            $stmt->bind_param('s', $recurso->nombre);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $stmt->close();
                // Insertar nuevo recurso
                $sql = "INSERT INTO recursos (nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, `tamaño_mb`, created_at, updated_at, eliminado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 0)";
                $ins = $this->conexion->prepare($sql);
                if ($ins) {
                    $nombre = $recurso->nombre;
                    $autor = $recurso->autor ?? null;
                    $departamento = $recurso->departamento ?? null;
                    $empresa = $recurso->empresa_institucion ?? ($recurso->empresa ?? null);
                    $fecha_creacion = $recurso->fecha_creacion ?? null;
                    $descripcion = $recurso->descripcion ?? null;
                    $nombre_archivo = $recurso->nombre_archivo ?? ($recurso->storedName ?? null);
                    $tipo_archivo = $recurso->tipo_archivo ?? null;
                    $url_archivo = $recurso->url_archivo ?? null;
                    $tamano_mb = isset($recurso->tamanio_mb) ? $recurso->tamanio_mb : (isset($recurso->tamaño_mb) ? $recurso->{'tamaño_mb'} : null);

                    // bind (usar s = string, d = double)
                    $ins->bind_param('ssssssssds', $nombre, $autor, $departamento, $empresa, $fecha_creacion, $descripcion, $nombre_archivo, $tipo_archivo, $url_archivo, $tamano_mb);
                    if ($ins->execute()) {
                        $data['status'] = 'success';
                        $data['message'] = 'Recurso agregado';
                        $data['id'] = $this->conexion->insert_id;
                    } else {
                        $data['message'] = 'Error al insertar recurso: ' . $ins->error;
                    }
                    $ins->close();
                } else {
                    $data['message'] = 'Error prepare insert: ' . $this->conexion->error;
                }
            } else {
                $stmt->close();
            }
        } else {
            $data['message'] = 'Error prepare check: ' . $this->conexion->error;
        }

        // Cierra la conexion
        $this->conexion->close();

        // Respuesta JSON
        $this->data = json_encode($data, JSON_PRETTY_PRINT);
    }


}


?>