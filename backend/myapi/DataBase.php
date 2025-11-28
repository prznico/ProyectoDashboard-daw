<?php

namespace MyAPI;

abstract class DataBase
{
    protected $conexion;

    public function __construct($db = 'marketzone', $host = 'localhost', $user = 'root', $password = 'N1n1c0l3.')
    {
        $this->conexion = @mysqli_connect($host, $user, $password, $db);
        
        /**
        * NOTA: si la conexión falló $conexion contendrá false
        **/

        if (!$this->conexion) {
            die('¡Base de datos NO conectada!');
        }
    }

    public function getData(){
        return $this->data;
    }
}
?>