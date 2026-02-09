<?php 

/**
 * ========================================
 * VALIDATOR - Validación de datos AJAX
 * ========================================
 * 
 * Clase auxiliar para validar datos y retornar
 * errores en formato AJAX automáticamente
 */

class AjaxValidator
{
    private $data;
    private $rules;
    private $errors = [];

    public function __construct($data, $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Validar datos
     */
    public function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        // Si hay errores, enviar respuesta de error y terminar
        if (!empty($this->errors)) {
            AjaxResponse::validationError($this->errors);
        }

        return true;
    }

    /**
     * Aplicar regla de validación
     */
    private function applyRule($field, $value, $rule)
    {
        // Separar regla y parámetros (ej: "min:5")
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $param = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field] = "El campo $field es obligatorio";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "El campo $field debe ser un email válido";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < $param) {
                    $this->errors[$field] = "El campo $field debe tener mínimo $param caracteres";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > $param) {
                    $this->errors[$field] = "El campo $field debe tener máximo $param caracteres";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field] = "El campo $field debe ser numérico";
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->errors[$field] = "El campo $field solo puede contener letras";
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->errors[$field] = "El campo $field solo puede contener letras y números";
                }
                break;
        }
    }

    /**
     * Método estático para validar directamente
     * 
     * Uso:
     *   AjaxValidator::make($_POST, [
     *       'nombre' => 'required|min:3',
     *       'email' => 'required|email'
     *   ]);
     */
    public static function make($data, $rules)
    {
        $validator = new self($data, $rules);
        return $validator->validate();
    }
}