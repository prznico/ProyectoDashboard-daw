<?php  
    namespace MyAPI\Read;
    use MyAPI\DataBase as DataBase;
    require_once __DIR__ . '/../DataBase.php';

    class Read extends DataBase{

        public function __construct($db) {
            parent::__construct($db);
        }

        
        public function listProduct(){
            // SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
            $data = array();

            // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
            if ( $result = $this->conexion->query("SELECT * FROM productos WHERE eliminado = 0") ) {
                // SE OBTIENEN LOS RESULTADOS
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                if(!is_null($rows)) {
                    // SE CODIFICAN A UTF-8 LOS DATOS Y SE MAPEAN AL ARREGLO DE RESPUESTA
                    foreach($rows as $num => $row) {
                        foreach($row as $key => $value) {
                            $data[$num][$key] = utf8_encode($value);
                        }
                    }
                }
                $result->free();
            } else {
                die('Query Error: '.mysqli_error($this->conexion));
            }
            $this->conexion->close();
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }

        public function search($producto){
            $data = array();
            // SE VERIFICA HABER RECIBIDO EL ID
            if( isset($producto['search']) ) {
                $search = $producto['search'];
                // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
                $sql = "SELECT * FROM productos WHERE (id = '{$search}' OR nombre LIKE '%{$search}%' OR marca LIKE '%{$search}%' OR detalles LIKE '%{$search}%') AND eliminado = 0";
                if ( $result = $this->conexion->query($sql) ) {
                    // SE OBTIENEN LOS RESULTADOS
                    $rows = $result->fetch_all(MYSQLI_ASSOC);

                    if(!is_null($rows)) {
                        // SE CODIFICAN A UTF-8 LOS DATOS Y SE MAPEAN AL ARREGLO DE RESPUESTA
                        foreach($rows as $num => $row) {
                            foreach($row as $key => $value) {
                                $data[$num][$key] = utf8_encode($value);
                            }
                        }
                    }
                    $result->free();
                } else {
                    die('Query Error: '.mysqli_error($this->conexion));
                }
                $this->conexion->close();
            }
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }

        public function single($producto){
            $id = $producto['id'];
            $sql = "SELECT * FROM productos WHERE id = $id";
            $result = $this->conexion -> query( $sql);
            if (!$result){
                die('Query failed');
            }

            $json = array();
            while($row = mysqli_fetch_array($result)) {
                // SE CONVIERTE A JSON
                $json[] = array(
                    'id' => $id,
                    'nombre' => $row['nombre'],
                    'precio' => $row['precio'],
                    'unidades' => $row['unidades'],
                    'modelo' => $row['modelo'],
                    'marca' => $row['marca'],
                    'detalles' => $row['detalles'],
                    'imagen' => $row['imagen']
                );
            }
            
            $result->free();
            $this->conexion->close();

            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data=json_encode($json[0], JSON_PRETTY_PRINT);
        }

        public function singleByName($producto){
            $data = array();
            // SE VERIFICA HABER RECIBIDO EL ID
            if( isset($producto['name']) ) {
                $search = $producto['name'];
                // SE REALIZA LA QUERY DE BÚSQUEDA Y AL MISMO TIEMPO SE VALIDA SI HUBO RESULTADOS
                $sql = "SELECT * FROM productos WHERE nombre = '{$search}' AND eliminado = 0";
                if ( $result = $this->conexion->query($sql) ) {
                    // SE OBTIENEN LOS RESULTADOS
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
        
                    if(!is_null($rows)) {
                        // SE CODIFICAN A UTF-8 LOS DATOS Y SE MAPEAN AL ARREGLO DE RESPUESTA
                        foreach($rows as $num => $row) {
                            foreach($row as $key => $value) {
                                $data[$num][$key] = utf8_encode($value);
                            }
                        }
                    }
                    $result->free();
                } else {
                    die('Query Error: '.mysqli_error($this->conexion));
                }
                $this->conexion->close();
            } 
            
            // SE HACE LA CONVERSIÓN DE ARRAY A JSON
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }

      

    }
?>