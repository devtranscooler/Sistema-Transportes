<?php
/**
 * Clase Model - La mamá de todos los modelos
 * 
 * Esta clase base permite que todos tus modelos hereden funcionalidades comunes
 * como: crear, leer, actualizar, eliminar (CRUD) sin tener que escribir SQL cada vez.
 * 
 */

class Model {
    protected $db;              // Conexión a la base de datos
    protected $table;           // Nombre de la tabla 
    protected $primaryKey = 'id'; // Llave primaria
    protected $fillable = [];   // Campos permitidos para llenar
    protected $attributes = []; // Datos actuales del modelo
    protected $exists = false;  // ¿Ya existe en BD?

    public function __construct() {
        // Inicializar conexión a BD
        $this->db = Database::getInstance();
    }

    /**
     * Método mágico para acceder a propiedades como si fueran normales
     * $usuario->nombre es más bonito que $usuario->getAttribute('nombre')
     */
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value) {
        // Solo permitir campos que estén en $fillable (seguridad)
        if (in_array($name, $this->fillable)) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * FIND - Buscar un registro por ID
     * 
     * Uso: $usuario = Usuario::find(5);
     */
    public static function find($id) {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} 
                WHERE {$instance->primaryKey} = ? 
                LIMIT 1";
        
        $result = $instance->db->query($sql, [$id]);
        
        if ($row = $result->fetch_assoc()) {
            $instance->fill($row);
            $instance->exists = true;
            return $instance;
        }
        
        return null;
    }

    /**
     * WHERE - Buscar con condiciones personalizadas
     * 
     * Uso: $usuarios = Usuario::where('email', 'email@example.com');
     * Uso: $activos = Usuario::where('status', 'activo')->get();
     */
    public static function where($column, $value) {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} 
                WHERE {$column} = ?";
        
        $result = $instance->db->query($sql, [$value]);
        
        $models = [];
        while ($row = $result->fetch_assoc()) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * ALL - Traer todos los registros
     * 
     * Uso: $usuarios = Usuario::all();
     */
    public static function all() {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table}";
        $result = $instance->db->query($sql);
        
        $models = [];
        while ($row = $result->fetch_assoc()) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * SAVE - Guardar o actualizar
     * 
     * Si el registro existe (tiene ID), hace UPDATE
     * Si no existe, hace INSERT
     * 
     * Uso: 
     *   $usuario->nombre = "User";
     *   $usuario->save();
     */
    public function save() {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * INSERT - Crear nuevo registro
     */
    protected function insert() {
        $fields = [];
        $placeholders = [];
        $values = [];

        // Construir query dinámicamente basado en $fillable
        foreach ($this->fillable as $field) {
            if (isset($this->attributes[$field])) {
                $fields[] = $field;
                $placeholders[] = '?';
                $values[] = $this->attributes[$field];
            }
        }

        $sql = "INSERT INTO {$this->table} 
                (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $result = $this->db->query($sql, $values);

        if ($result) {
            // Obtener el ID del nuevo registro
            $this->attributes[$this->primaryKey] = $this->db->getLastInsertId();
            $this->exists = true;
            return true;
        }

        return false;
    }

    /**
     * UPDATE - Actualizar registro existente
     */
    protected function update() {
        $fields = [];
        $values = [];

        foreach ($this->fillable as $field) {
            if (isset($this->attributes[$field])) {
                $fields[] = "$field = ?";
                $values[] = $this->attributes[$field];
            }
        }

        $values[] = $this->attributes[$this->primaryKey];

        $sql = "UPDATE {$this->table} 
                SET " . implode(', ', $fields) . " 
                WHERE {$this->primaryKey} = ?";

        return $this->db->query($sql, $values);
    }

    /**
     * DELETE - Eliminar registro
     * 
     * Uso: $usuario->delete();
     */
    public function delete() {
        if (!$this->exists) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} 
                WHERE {$this->primaryKey} = ?";

        return $this->db->query($sql, [$this->attributes[$this->primaryKey]]);
    }

    /**
     * FILL - Llenar el modelo con datos
     */
    protected function fill(array $data) {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * TO_ARRAY - Convertir a array
     */
    public function toArray() {
        return $this->attributes;
    }

    /**
     * TO_JSON - Convertir a JSON
     */
    public function toJson() {
        return json_encode($this->attributes);
    }
}