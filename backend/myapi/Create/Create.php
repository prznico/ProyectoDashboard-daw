<?php
namespace MyAPI\Create;
require_once __DIR__ . '/../DataBase.php';
use MyAPI\DataBase as DataBase;

class Create extends DataBase{
    public function __construct($db) {
        parent::__construct($db);
    }

    public function add($producto){
        $data = array(
            'status'  => 'error',
            'message' => 'Ya existe un producto con ese nombre'
        );
        // SE TRANSFORMA EL STRING DEL JASON A OBJETO
        // SE ASUME QUE LOS DATOS YA FUERON VALIDADOS ANTES DE ENVIARSE
        $sql = "SELECT * FROM productos WHERE nombre = '{$producto->nombre}' AND eliminado = 0";
        $result = $this->conexion->query($sql);
        
        if ($result->num_rows == 0) {
            $this->conexion->set_charset("utf8");
            $sql = "INSERT INTO productos VALUES (null, '{$producto->nombre}', '{$producto->marca}', '{$producto->modelo}', {$producto->precio}, '{$producto->detalles}', {$producto->unidades}, '{$producto->imagen}', 0)";
            if($this->conexion->query($sql)){
                $data['status'] =  "success";
                $data['message'] =  "Producto agregado";
            } else {
                $data['message'] = "ERROR: No se ejecuto $sql. " . mysqli_error($this->conexion);
            }
        }

        $result->free();
        // Cierra la conexion
        $this->conexion->close();
    
        // SE HACE LA CONVERSIÓN DE ARRAY A JSON
        $this->data = json_encode($data, JSON_PRETTY_PRINT);        
    }   


}


?>