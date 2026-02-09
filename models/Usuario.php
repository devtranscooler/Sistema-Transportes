<?php
require_once __DIR__ . '/../core/Model.php';
/**
 * Modelo Usuario
 * 
 * Maneja toda la lógica relacionada con usuarios:
 * - Autenticación (login/logout)
 * - Gestión de permisos
 * - CRUD de usuarios
 */
class Usuario extends Model
{
    // Nombre de la tabla en la BD
    protected $table = 'usuarios';

    // Llave primaria
    protected $primaryKey = 'id';

    // Campos que se pueden llenar (protección contra mass assignment)
    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'email',
        'password',
        'idRol',
        'fecNac',
        'noEmpleado',
        'movil',
        'foto',
        'fecContratacion',
        'diasVacaciones',
        'diasVacDisfrutados',
        'estatus',
        'puesto',
        'area',
        'cedis',
        'telefono',
        'jefeInmediato'
    ];
    // Campos protejidos (protecion contra actualizaciones masivas)
    protected $protected = [
        'password'
    ];

    /**
     * LOGIN - Autenticar usuario
     * 
     * Este método verifica las credenciales y retorna el usuario si es válido
     * ¡IMPORTANTE! Usa prepared statements para evitar SQL Injection
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña sin encriptar
     * @return Usuario|null Retorna el usuario si es válido, null si no
     */
    public static function login($email, $password)
    {
        $instance = new static();

        // Buscar usuario por email
        $sql = "SELECT * FROM {$instance->table} 
                WHERE email = ? 
                AND estatus = 'ACTIVO' 
                LIMIT 1";

        $result = $instance->db->query($sql, [$email]);

        if ($row = $result->fetch_assoc()) {
            // Verificar contraseña usando el stored procedure
            // Nota: Idealmente deberías usar password_hash() y password_verify()
            $checkSql = "CALL p_check_user(?, ?)";
            $checkResult = $instance->db->query($checkSql, [$email, $password]);

            if ($checkData = $checkResult->fetch_assoc()) {
                if ($checkData['exist'] == 1) {
                    // Crear instancia del usuario
                    $usuario = new static();
                    $usuario->fill($row);
                    $usuario->exists = true;

                    return $usuario;
                }
            }
        }

        return null;
    }

    /**
     * INICIO DE SESIÓN - Guardar usuario en sesión
     * 
     * Guarda los datos importantes en $_SESSION para usar en toda la app
     */
    public function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['ID_USUARIO'] = $this->id;
        $_SESSION['NAME'] = $this->getNombreCompleto();
        $_SESSION['EMAIL'] = $this->email;
    }

    /**
     * LOGOUT - Cerrar sesión
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        session_unset();
    }

    /**
     * GET CURRENT - Obtener usuario actual de la sesión
     * 
     * @return Usuario|null
     */
    public static function current()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['ID_USUARIO'])) {
            return static::find($_SESSION['ID_USUARIO']);
        }

        return null;
    }

    /**
     * IS AUTHENTICATED - Verificar si hay sesión activa
     * 
     * @return bool
     */
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['ID_USUARIO']);
    }

    /**
     * GET NOMBRE COMPLETO - Concatenar nombre completo
     * 
     * @return string
     */
    public function getNombreCompleto()
    {
        return trim("{$this->nombre} {$this->apellidoP} {$this->apellidoM}");
    }

    /**
     * GET MENUS - Obtener menús del usuario
     * 
     * @return array
     */
    public function getMenus(): array
    {
        $sql = "SELECT 
                    m.id_menu,
                    m.id_parent,
                    m.nombre,
                    m.nivel,
                    m.url,
                    m.orden,
                    m.icon
                FROM menu_usuarios mu
                INNER JOIN menu m ON mu.id_menu = m.id_menu
                WHERE mu.id_usuario = ?
                    AND m.status = 'activo'
                    AND m.tab = 0
                ORDER BY m.orden, m.id_menu";

        $result = $this->db->query($sql, [$this->id]);

        $menus = [];
        while ($row = $result->fetch_assoc()) {
            // IMPORTANTE: Normalizar id_parent a null si está vacío
            $row['id_parent'] = empty($row['id_parent']) ? null : $row['id_parent'];
            $menus[] = $row;
        }

        return $this->buildMenuTree($menus);
    }

    /**
     * BUILD MENU TREE - Construir árbol de menús (VERSIÓN CORREGIDA)
     * 
     * Convierte una lista plana de menús en estructura jerárquica
     * Ahora maneja correctamente NULL, '', y valores vacíos
     */
    private function buildMenuTree($menus, $parentId = null): array
    {
        $branch = [];

        foreach ($menus as $menu) {
            // SOLUCIÓN: Comparar considerando NULL, '', y false como equivalentes
            $menuParent = empty($menu['id_parent']) ? null : $menu['id_parent'];
            $searchParent = empty($parentId) ? null : $parentId;

            if ($menuParent === $searchParent) {
                // Buscar hijos de este menú
                $children = $this->buildMenuTree($menus, $menu['id_menu']);

                if (!empty($children)) {
                    $menu['children'] = $children;
                }

                $branch[] = $menu;
            }
        }

        return $branch;
    }

    /**
     * HAS PERMISSION - Verificar si tiene permiso
     * 
     * @param string $permiso Nombre del permiso
     * @return bool
     */
    public function hasPermission($permiso)
    {
        $sql = "SELECT COUNT(*) as tiene
                FROM permisos_usuarios pu
                INNER JOIN permisos p ON pu.id_permiso = p.id_permiso
                WHERE pu.id_usuario = ?
                    AND p.nombre = ?
                    AND p.activo = 1";

        $result = $this->db->query($sql, [$this->id, $permiso]);
        $row = $result->fetch_assoc();

        return $row['tiene'] > 0;
    }

    /**
     * BEFORE SAVE - Hook antes de guardar
     * 
     * Aquí puedes encriptar la contraseña automáticamente
     */
    protected function insert()
    {
        // Encriptar password antes de guardar
        if (isset($this->attributes['password'])) {
            $this->attributes['password'] = password_hash(
                $this->attributes['password'],
                PASSWORD_BCRYPT
            );
        }

        return parent::insert();
    }

    /**
     * SET PASSWORD - Establecer contraseña manualmente
     * 
     * Uso desde controlador: $usuario->setPassword('nueva123');
     */
    public function setPassword($plainPassword)
    {
        $this->attributes['password'] = password_hash($plainPassword, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * SAVE  erencia del padre 
     * 
     * Sirve para hasear la password 
     */
    public function save()
    {
        if (in_array('password', $this->modifiedFields)) {
            if (!empty($this->password) && !$this->isPasswordHashed($this->password)) {
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            }
        }

        return parent::save();
    }

    /**
     * Verificar si password está hasheado
     */
    private function isPasswordHashed($password)
    {
        return preg_match('/^\$2[ayb]\$.{56}$/', $password);
    }

    /**
     * VALIDATE - Validar datos del usuario
     * 
     * @return array Errores encontrados
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->attributes['nombre'])) {
            $errors[] = "El nombre es requerido";
        }

        if (empty($this->attributes['apellidoP'])) {
            $errors[] = "El apellido paterno es requerido";
        }

        if (empty($this->attributes['email'])) {
            $errors[] = "El email es requerido";
        } elseif (!filter_var($this->attributes['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido";
        }

        // Validar que el email no exista (solo en creación o si cambió)
        if (!$this->exists || $this->emailChanged()) {
            $existing = static::where('email', $this->attributes['email']);
            if (!empty($existing) && $existing[0]->id != $this->id) {
                $errors[] = "El email ya está registrado";
            }
        }

        // Validar contraseña solo en creación
        if (!$this->exists && empty($this->attributes['password'])) {
            $errors[] = "La contraseña es requerida";
        }

        if (
            !empty($this->attributes['password']) &&
            strlen($this->attributes['password']) < 6
        ) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }

        /* if (empty($this->attributes['idRol'])) {
            $errors[] = "El rol es requerido";
        } */

        return $errors;
    }

    /**
     * VALIDATE - Validar si el email cambio
     * 
     * @return bool 
     */
    private function emailChanged()
    {
        if (!$this->exists) return false;

        $original = static::find($this->id);
        return $original->email !== $this->attributes['email'];
    }
}
