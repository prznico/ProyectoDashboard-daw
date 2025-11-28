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
                'message' => 'Ya existe un producto con ese nombre'
            );
                // SE TRANSFORMA EL STRING DEL JASON A OBJETO
                // SE ASUME QUE LOS DATOS YA FUERON VALIDADOS ANTES DE ENVIARSE
                $sql = "UPDATE productos SET nombre = '{$producto->nombre}', marca = '{$producto->marca}', modelo = '{$producto->modelo}', precio = '{$producto->precio}', detalles = '{$producto->detalles}', unidades = '{$producto->unidades}', imagen = '{$producto->imagen}' WHERE id = {$producto->id}";
        
                // Execute the statement
                if ($this->conexion->query($sql) === TRUE) {
                    $data['status'] =  "success";
                    $data['message'] =  "Producto editado correctamente";
                } else {
                    $data['message'] = "ERROR: No se ejecutó la consulta. " . $this->conexion->error;
                }
        
                // Cierra la conexion
                $this->conexion->close();
        
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }
    }
?>