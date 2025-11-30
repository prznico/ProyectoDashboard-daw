<?php
    namespace MyAPI\Read;
    use MyAPI\DataBase as DataBase;
    require_once __DIR__ . '/../DataBase.php';

    class Read extends DataBase{

        public function __construct($db) {
            parent::__construct($db);
        }

        public function listProduct(){
            $data = array();
            $sql = "SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, `tamaño_mb`, created_at, updated_at FROM recursos WHERE eliminado = 0";
            if ($result = $this->conexion->query($sql)) {
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                if (!is_null($rows)) {
                    foreach ($rows as $num => $row) {
                        foreach ($row as $key => $value) {
                            $data[$num][$key] = is_string($value) ? utf8_encode($value) : $value;
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
            if (isset($producto['search'])) {
                $search = $producto['search'];
                $sql = "SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, `tamaño_mb`, created_at, updated_at FROM recursos WHERE (id = ? OR nombre LIKE ? OR autor LIKE ? OR descripcion LIKE ?) AND eliminado = 0";
                $stmt = $this->conexion->prepare($sql);
                if ($stmt) {
                    $like = "%{$search}%";
                    $stmt->bind_param('isss', $search, $like, $like, $like);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        $rows = $result->fetch_all(MYSQLI_ASSOC);
                        if (!is_null($rows)) {
                            foreach ($rows as $num => $row) {
                                foreach ($row as $key => $value) {
                                    $data[$num][$key] = is_string($value) ? utf8_encode($value) : $value;
                                }
                            }
                        }
                        $result->free();
                    }
                    $stmt->close();
                } else {
                    die('Query Error: '.mysqli_error($this->conexion));
                }
                $this->conexion->close();
            }
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }

        public function single($producto){
            if (!isset($producto['id'])) {
                $this->data = json_encode(['status' => 'error', 'message' => 'ID no proporcionado'], JSON_PRETTY_PRINT);
                return;
            }
            $id = intval($producto['id']);
            $sql = "SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, `tamaño_mb`, created_at, updated_at FROM recursos WHERE id = ? LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                die('Query failed: ' . mysqli_error($this->conexion));
            }
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if (!$row) {
                $this->data = json_encode(['status' => 'error', 'message' => 'Recurso no encontrado'], JSON_PRETTY_PRINT);
                $stmt->close();
                $this->conexion->close();
                return;
            }
            $json = array(
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'autor' => $row['autor'],
                'departamento' => $row['departamento'],
                'empresa_institucion' => $row['empresa_institucion'],
                'fecha_creacion' => $row['fecha_creacion'],
                'descripcion' => $row['descripcion'],
                'nombre_archivo' => $row['nombre_archivo'],
                'tipo_archivo' => $row['tipo_archivo'],
                'url_archivo' => $row['url_archivo'],
                'tamanio_mb' => $row['tamaño_mb'] ?? $row['tamano_mb'] ?? null,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
            $result->free();
            $stmt->close();
            $this->conexion->close();
            $this->data = json_encode($json, JSON_PRETTY_PRINT);
        }

        public function singleByName($producto){
            $data = array();
            if (isset($producto['name'])) {
                $search = $producto['name'];
                $sql = "SELECT id, nombre, autor, departamento, empresa_institucion, fecha_creacion, descripcion, nombre_archivo, tipo_archivo, url_archivo, `tamaño_mb`, created_at, updated_at FROM recursos WHERE nombre = ? AND eliminado = 0";
                $stmt = $this->conexion->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('s', $search);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        $rows = $result->fetch_all(MYSQLI_ASSOC);
                        if (!is_null($rows)) {
                            foreach ($rows as $num => $row) {
                                foreach ($row as $key => $value) {
                                    $data[$num][$key] = is_string($value) ? utf8_encode($value) : $value;
                                }
                            }
                        }
                        $result->free();
                    }
                    $stmt->close();
                } else {
                    die('Query Error: '.mysqli_error($this->conexion));
                }
                $this->conexion->close();
            }
            $this->data = json_encode($data, JSON_PRETTY_PRINT);
        }


    }
?>