<?php
/**
 * Clase Database para conexión con Oracle
 * Sistema de Gestión Hospitalaria Pegasus
 */
class Database {
    private $connection;
    private $username;
    private $password;
    private $connectionString;
    private static $instance = null;
    
    /**
     * Constructor privado para singleton
     */
    private function __construct($config) {
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->connectionString = $config['connection_string'];
        $this->connect();
    }
    
    /**
     * Obtener instancia única de la base de datos (patrón singleton)
     */
    public static function getInstance($config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                throw new Exception("La configuración de la base de datos es requerida");
            }
            self::$instance = new Database($config);
        }
        return self::$instance;
    }
    
    /**
     * Establece la conexión a Oracle
     */
    private function connect() {
        try {
            // Establece la conexión a Oracle
            $this->connection = oci_connect(
                $this->username,
                $this->password,
                $this->connectionString,
                'AL32UTF8'
            );
            
            if (!$this->connection) {
                $error = oci_error();
                throw new Exception("Error de conexión a Oracle: " . $error['message']);
            }
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            throw new Exception("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta un procedimiento almacenado simple sin parámetros de salida
     * 
     * @param string $procedureName Nombre del procedimiento almacenado
     * @param array $params Parámetros del procedimiento [nombre => valor]
     * @return boolean Éxito o fracaso de la operación
     */
    public function executeProcedure($procedureName, $params = array()) {
        try {
            // Crear el string de llamada al procedimiento
            $callString = "BEGIN $procedureName(";
            
            $paramCount = count($params);
            $i = 0;
            
            // Construir los placeholders para los parámetros
            foreach ($params as $name => $value) {
                $callString .= ":" . $name;
                if (++$i < $paramCount) {
                    $callString .= ", ";
                }
            }
            
            $callString .= "); END;";
            
            // Preparar la sentencia
            $statement = oci_parse($this->connection, $callString);
            if (!$statement) {
                $error = oci_error($this->connection);
                throw new Exception("Error al preparar la sentencia: " . $error['message']);
            }
            
            // Enlazar los parámetros
            foreach ($params as $name => $value) {
                if (!oci_bind_by_name($statement, ":" . $name, $params[$name])) {
                    $error = oci_error($statement);
                    throw new Exception("Error al enlazar el parámetro '$name': " . $error['message']);
                }
            }
            
            // Ejecutar el procedimiento
            $result = oci_execute($statement);
            if (!$result) {
                $error = oci_error($statement);
                throw new Exception("Error al ejecutar el procedimiento '$procedureName': " . $error['message']);
            }
            
            // Liberar recursos
            oci_free_statement($statement);
            
            return true;
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            throw new Exception("Error al llamar al procedimiento '$procedureName': " . $e->getMessage());
        }
    }

    /**
     * Ejecuta una consulta simple y devuelve un array asociativo con los resultados
     * 
     * @param string $query Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta [nombre => valor]
     * @return array Resultado de la consulta
     */
    public function query($query, $params = array()) {
        try {
            // Preparar la sentencia
            $statement = oci_parse($this->connection, $query);
            if (!$statement) {
                $error = oci_error($this->connection);
                throw new Exception("Error al preparar la consulta: " . $error['message']);
            }
            
            // Enlazar los parámetros
            foreach ($params as $name => $value) {
                if (!oci_bind_by_name($statement, ":" . $name, $params[$name])) {
                    $error = oci_error($statement);
                    throw new Exception("Error al enlazar el parámetro '$name': " . $error['message']);
                }
            }
            
            // Ejecutar la consulta
            $result = oci_execute($statement);
            if (!$result) {
                $error = oci_error($statement);
                throw new Exception("Error al ejecutar la consulta: " . $error['message']);
            }
            
            // Obtener los resultados
            $data = [];
            while ($row = oci_fetch_assoc($statement)) {
                $data[] = $row;
            }
            
            // Liberar recursos
            oci_free_statement($statement);
            
            return $data;
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            throw new Exception("Error al ejecutar la consulta: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta una consulta y devuelve una sola fila como array asociativo
     * 
     * @param string $query Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta [nombre => valor]
     * @return array|null Una fila de resultados o null si no hay resultados
     */
    public function queryOne($query, $params = array()) {
        $result = $this->query($query, $params);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Ejecuta una consulta y devuelve un valor único
     * 
     * @param string $query Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta [nombre => valor]
     * @param string $field Campo a devolver (por defecto, el primero)
     * @return mixed|null Valor único o null si no hay resultados
     */
    public function queryValue($query, $params = array(), $field = null) {
        $row = $this->queryOne($query, $params);
        
        if (!$row) {
            return null;
        }
        
        if ($field !== null && isset($row[$field])) {
            return $row[$field];
        }
        
        // Si no se especificó un campo o no existe, devolver el primer valor
        return reset($row);
    }
    
    /**
     * Iniciar una transacción
     */
    public function beginTransaction() {
        // Oracle inicia automáticamente una transacción cuando se ejecuta la primera sentencia
        return true;
    }
    
    /**
     * Confirmar una transacción
     */
    public function commit() {
        $result = oci_commit($this->connection);
        if (!$result) {
            $error = oci_error($this->connection);
            $this->logError("Error al hacer commit: " . $error['message']);
            throw new Exception("Error al confirmar la transacción: " . $error['message']);
        }
        return true;
    }
    
    /**
     * Revertir una transacción
     */
    public function rollback() {
        $result = oci_rollback($this->connection);
        if (!$result) {
            $error = oci_error($this->connection);
            $this->logError("Error al hacer rollback: " . $error['message']);
            throw new Exception("Error al revertir la transacción: " . $error['message']);
        }
        return true;
    }
    
    /**
     * Cerrar la conexión a la base de datos
     */
    public function close() {
        if ($this->connection) {
            oci_close($this->connection);
            $this->connection = null;
            self::$instance = null;
        }
    }
    
    /**
     * Registrar errores en el archivo de log
     */
    private function logError($message) {
        $logFile = dirname(__DIR__) . '/logs/error.log';
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        
        // Asegurar que el directorio existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        // Escribir en el archivo de log
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Destructor - Cierra la conexión automáticamente
     */
    public function __destruct() {
        $this->close();
    }
}