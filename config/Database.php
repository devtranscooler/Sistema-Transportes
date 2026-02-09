<?php
/**
 * Clase Database - Singleton Pattern
 * 
 * Esta clase maneja la conexión a la base de datos usando el patrón Singleton
 * (solo una instancia en toda la aplicación) y prepared statements para seguridad.
 * 
 */

class Database {
    private static $instance = null;  // La única instancia
    private $connection;              // Conexión MySQLi
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;

    /**
     * Constructor privado - Nadie puede hacer "new Database()"
     * Solo se puede obtener la instancia con getInstance()
     */
    private function __construct() {
        // Leer configuración desde bd.ini
        $config = $this->loadConfig();
        
        $this->host = $config['IP'];
        $this->username = $config['USR'];
        $this->password = $config['PWD'];
        $this->database = $config['DB'];
        $this->port = $config['PORT'] ?? 3306;

        $this->connect();
    }

    /**
     * GET INSTANCE - Obtener la única instancia
     * 
     * Uso: $db = Database::getInstance();
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * LOAD CONFIG - Cargar configuración
     */
    private function loadConfig() {
        $configFile = __DIR__ . '/../system/bd.ini';
        
        if (!file_exists($configFile)) {
            throw new Exception("Archivo de configuración no encontrado: $configFile");
        }

        $config = parse_ini_file($configFile);
        
        if ($config === false) {
            throw new Exception("Error al leer archivo de configuración");
        }

        return $config;
    }

    /**
     * CONNECT - Establecer conexión
     */
    private function connect() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );

            // Verificar errores de conexión
            if ($this->connection->connect_error) {
                throw new Exception(
                    "Error de conexión: " . $this->connection->connect_error
                );
            }

            // Configurar charset UTF-8 para evitar problemas con acentos
            $this->connection->set_charset("utf8mb4");

        } catch (Exception $e) {
            die("Error fatal de base de datos: " . $e->getMessage());
        }
    }

    /**
     * QUERY - Ejecutar consulta con prepared statements
     * 
     * Este método es LA CLAVE de la seguridad. Los prepared statements
     * evitan SQL Injection separando los datos de la consulta.
     * 
     * Uso:
     *   $db->query("SELECT * FROM usuarios WHERE email = ?", ['juan@mail.com']);
     *   $db->query("INSERT INTO logs (accion, usuario) VALUES (?, ?)", ['login', 'Juan']);
     * 
     * @param string $sql Consulta SQL con ? como placeholders
     * @param array $params Parámetros a bindear
     * @return mysqli_result|bool
     */
    public function query($sql, $params = []) {
        // Si no hay parámetros, ejecutar directamente
        if (empty($params)) {
            $result = $this->connection->query($sql);
            
            if (!$result) {
                $this->handleError($sql);
            }
            
            return $result;
        }

        // Preparar statement
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            $this->handleError($sql);
        }

        // Bindear parámetros dinámicamente
        if (!empty($params)) {
            // Detectar tipos de datos (s=string, i=integer, d=double, b=blob)
            $types = '';
            $bindParams = [];

            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 's'; // Por defecto string
                }
                $bindParams[] = $param;
            }

            // Bind params usando call_user_func_array
            $stmt->bind_param($types, ...$bindParams);
        }

        // Ejecutar
        $stmt->execute();

        // Si la consulta devuelve resultados (SELECT)
        if ($stmt->field_count > 0) {
            return $stmt->get_result();
        }

        // Si NO devuelve resultados (INSERT, UPDATE, DELETE)
        return $stmt->affected_rows >= 0;
    }

    /**
     * FETCH - Ejecutar y obtener un solo resultado
     */
    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    /**
     * FETCH ALL - Ejecutar y obtener todos los resultados
     */
    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        $rows = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }

    /**
     * GET LAST INSERT ID - Obtener ID del último INSERT
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * BEGIN TRANSACTION - Iniciar transacción
     * 
     * Útil cuando necesitas que varias operaciones sean atómicas
     * (todas se ejecutan o ninguna)
     */
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    /**
     * COMMIT - Confirmar transacción
     */
    public function commit() {
        $this->connection->commit();
    }

    /**
     * ROLLBACK - Revertir transacción
     */
    public function rollback() {
        $this->connection->rollback();
    }

    /**
     * HANDLE ERROR - Manejar errores
     */
    private function handleError($sql) {
        $error = $this->connection->error;
        
        // En producción, loguear el error en lugar de mostrarlo
        error_log("Database Error: $error");
        error_log("Query: $sql");
        
        throw new Exception("Error en la consulta: " . $error);
    }

    /**
     * ESCAPE STRING - Escapar string (para casos legacy)
     * 
     * Nota: Es mejor usar prepared statements, pero esto está
     * disponible para código heredado
     */
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    /**
     * Evitar clonación (parte del patrón Singleton)
     */
    private function __clone() {}

    /**
     * Evitar deserialización (parte del patrón Singleton)
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }

    /**
     * Cerrar conexión al destruir el objeto
     */
    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

