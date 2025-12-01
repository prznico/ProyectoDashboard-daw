<?php
    namespace MyAPI\Delete;
    require_once __DIR__ . '/../DataBase.php';
    use MyAPI\DataBase as DataBase;

    class Delete extends DataBase{
        public function __construct($db) {
            parent::__construct($db);
        }

        public function delete($producto){
            $data = array(
                'status'  => 'error',
                'message' => 'La consulta falló'
            );
            // SE VERIFICA HABER RECIBIDO EL ID
            if( isset($producto['id']) ) {
                $id = intval($producto['id']);
                $stmt = $this->conexion->prepare('UPDATE recursos SET eliminado = 1, updated_at = NOW() WHERE id = ?');
                if ($stmt) {
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute()) {
                        $data['status'] =  "success";
                        $data['message'] =  "Recurso eliminado";
                    } else {
                        $data['message'] = "ERROR execute: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $data['message'] = 'ERROR prepare: ' . $this->conexion->error;
                }
                $this->conexion->close();
            }
            
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }

    }

?>