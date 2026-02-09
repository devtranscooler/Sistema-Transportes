<?php
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $protected = [];
    protected $attributes = [];
    protected $exists = false;
    
    // 🔥 Inicializar como array vacío directamente
    protected $modifiedFields = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function __set($name, $value) {
        if (in_array($name, $this->fillable)) {
            // 🔥 Asegurar que modifiedFields esté inicializado
            if (!is_array($this->modifiedFields)) {
                $this->modifiedFields = [];
            }
            
            if (!isset($this->attributes[$name]) || $this->attributes[$name] !== $value) {
                $this->modifiedFields[] = $name;
            }
            
            $this->attributes[$name] = $value;
        }
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    /**
     * FIND - Buscar por ID
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
            
            // 🔥 Asegurar que modifiedFields esté inicializado
            $instance->modifiedFields = [];
            
            return $instance;
        }
        
        return null;
    }

    /**
     * WHERE - Buscar con condiciones
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
            
            // 🔥 Asegurar que modifiedFields esté inicializado
            $model->modifiedFields = [];
            
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * ALL - Traer todos
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
            
            // 🔥 Asegurar que modifiedFields esté inicializado
            $model->modifiedFields = [];
            
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * SAVE
     */
    public function save() {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * INSERT
     */
    protected function insert() {
        $fields = [];
        $placeholders = [];
        $values = [];

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
            $this->attributes[$this->primaryKey] = $this->db->getLastInsertId();
            $this->exists = true;
            $this->modifiedFields = [];
            return true;
        }

        return false;
    }

    /**
     * UPDATE
     */
    protected function update() {
        $fields = [];
        $values = [];

        foreach ($this->fillable as $field) {
            // SALTAR campos protegidos que NO fueron modificados
            if (in_array($field, $this->protected)) {
                // Asegurar que modifiedFields sea un array
                if (!is_array($this->modifiedFields)) {
                    $this->modifiedFields = [];
                }
                
                if (!in_array($field, $this->modifiedFields)) {
                    continue;
                }
            }

            if (isset($this->attributes[$field])) {
                $fields[] = "$field = ?";
                $values[] = $this->attributes[$field];
            }
        }

        if (empty($fields)) {
            return true;
        }

        $values[] = $this->attributes[$this->primaryKey];

        
        $sql = "UPDATE {$this->table} 
                SET " . implode(', ', $fields) . " 
                WHERE {$this->primaryKey} = ?";
                
        $result = $this->db->query($sql, $values);
                
        $this->modifiedFields = [];
        
        return $result;
    }

    /**
     * DELETE
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
     * FILL
     */
    public function fill(array $data) {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * TO_ARRAY
     */
    public function toArray() {
        return $this->attributes;
    }

    /**
     * TO_JSON
     */
    public function toJson() {
        return json_encode($this->attributes);
    }
}