<?php

namespace MyAPI;

class DataBase
{
    protected $conexion;
    protected $data = [];

    /**
     * Constructor: establece la conexión con la base de datos.
     * Mantiene compatibilidad con llamadas existentes que pasan el nombre
     * de la base de datos como primer parámetro.
     * @param string $db Nombre de la base de datos (por defecto 'dashboard_recursos')
     * @param string $host Host de la base de datos
     * @param string $user Usuario
     * @param string $pass Contraseña
     */
    public function __construct($db = 'dashboard_recursos', $host = 'localhost', $user = 'root', $pass = 'N1n1c0l3.')
    {
        $this->conexion = mysqli_connect($host, $user, $pass, $db);

        if (!$this->conexion) {
            throw new \Exception('¡Base de datos NO conectada!: ' . mysqli_connect_error());
        }

        // Forzar charset UTF-8 (utf8mb4)
        mysqli_set_charset($this->conexion, 'utf8mb4');
    }

    /**
     * Devuelve la conexión activa
     */
    public function getConnection()
    {
        return $this->conexion;
    }

    /**
     * Devuelve los datos preparados por los métodos hijos
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Helper: escapar valores (usa la conexión actual)
     */
    protected function escape($value)
    {
        return mysqli_real_escape_string($this->conexion, (string)$value);
    }

    /**
     * Registrar un acceso en la bitácora (login/logout/vista_recurso)
     * Retorna true si se insertó correctamente, false en caso de error.
     */
    public function registrarAcceso($usuario_id = null, $tipo_acceso = 'vista_recurso', $recurso_id = null, $ip_address = null)
    {
        $ip = $ip_address ?? ($_SERVER['REMOTE_ADDR'] ?? '');

        $sql = "INSERT INTO bitacora_acceso (usuario_id, tipo_acceso, recurso_id, fecha_hora, ip_address) VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log('registrarAcceso prepare error: ' . $this->conexion->error);
            return false;
        }

        // usar NULL cuando corresponda
        $usuario_val = is_null($usuario_id) ? null : intval($usuario_id);
        $recurso_val = is_null($recurso_id) ? null : intval($recurso_id);
        // bind_param no acepta null directamente para tipos 'i', así que usamos 's' y convertimos
        $tipo_val = (string)$tipo_acceso;
        $ip_val = (string)$ip;

        // Para simplificar la compatibilidad de tipos, bindear todo como strings
        $ok = $stmt->bind_param('siss', $usuario_val === null ? null : (string)$usuario_val, $tipo_val, $recurso_val === null ? null : (string)$recurso_val, $ip_val);
        if ($ok === false) {
            error_log('registrarAcceso bind_param error: ' . $stmt->error);
            $stmt->close();
            return false;
        }

        $exec = $stmt->execute();
        if ($exec === false) {
            error_log('registrarAcceso execute error: ' . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }

    /**
     * Registrar una descarga en la bitácora
     * Retorna true si se insertó correctamente, false en caso de error.
     */
    public function registrarDescarga($usuario_id = null, $recurso_id, $ip_address = null)
    {
        $ip = $ip_address ?? ($_SERVER['REMOTE_ADDR'] ?? '');

        $sql = "INSERT INTO bitacora_descargas (usuario_id, recurso_id, fecha_descarga, ip_address) VALUES (?, ?, NOW(), ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log('registrarDescarga prepare error: ' . $this->conexion->error);
            return false;
        }

        $usuario_val = is_null($usuario_id) ? null : intval($usuario_id);
        $recurso_val = intval($recurso_id);
        $ip_val = (string)$ip;

        $ok = $stmt->bind_param('iis', $usuario_val === null ? null : $usuario_val, $recurso_val, $ip_val);
        if ($ok === false) {
            error_log('registrarDescarga bind_param error: ' . $stmt->error);
            $stmt->close();
            return false;
        }

        $exec = $stmt->execute();
        if ($exec === false) {
            error_log('registrarDescarga execute error: ' . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }

    /**
     * Destructor: cierra la conexión automáticamente al destruir el objeto
     */
    public function __destruct()
    {
        if ($this->conexion) {
            mysqli_close($this->conexion);
        }
    }
}