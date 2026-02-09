<?php

/**
 * AuthController - Controlador de Autenticación
 * 
 * Maneja todo lo relacionado con login, logout y registro
 * 
 */

class AuthController
{

    /**
     * SHOW LOGIN - Mostrar formulario de login
     * 
     * Ruta: GET /login
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Usuario::isAuthenticated()) {
            View::redirect('/dashboard');
            return;
        }

        // Mostrar vista de login
        View::render('layouts/login', [
            'error' => flash('error')
        ], 'login'); // Usa layout 'login' en vez de 'main'
    }

    /**
     * LOGIN - Procesar login
     * 
     * Ruta: POST /login
     */
    public function login()
    {
        // Obtener datos del formulario
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';


        // Validar que no estén vacíos
        if (empty($email) || empty($password)) {
            flash('error', 'Email y contraseña son requeridos');
            View::redirect('/login');
            return;
        }

        try {
            // Intentar autenticar
            $usuario = Usuario::login($email, $password);
            if ($usuario) {

                // Login exitoso
                $usuario->startSession();

                // Registrar en logs (opcional)
                $this->logAccess($usuario->id, 'login');

                // Redirigir al dashboard
                View::redirect('/dashboard');
            } else {
                
                // Credenciales inválidas
                flash('error', 'Email o contraseña incorrectos');
                View::redirect('/login');
            }
        } catch (Exception $e) {
            // Error del servidor
            error_log("Error en login: " . $e->getMessage());
            flash('error', 'Error del servidor. Intenta de nuevo.');
            View::redirect('/login');
        }
    }

    /**
     * LOGOUT - Cerrar sesión
     * 
     * Ruta: GET/POST /logout
     */
    public function logout()
    {
        // Registrar en logs antes de cerrar sesión
        if (Usuario::isAuthenticated()) {
            $usuario = Usuario::current();
            $this->logAccess($usuario->id, 'logout');
        }

        // Cerrar sesión
        Usuario::logout();

        // Redirigir al login
        flash('success', 'Sesión cerrada correctamente');
        View::redirect('/login');
    }

    /**
     * SHOW REGISTER - Mostrar formulario de registro
     * 
     * Ruta: GET /register
     */
    public function showRegister()
    {
        if (Usuario::isAuthenticated()) {
            View::redirect('/dashboard');
            return;
        }

        View::render('auth/register', [], 'login');
    }

    /**
     * REGISTER - Procesar registro
     * 
     * Ruta: POST /register
     */
    public function register()
    {
        // Crear nuevo usuario
        $usuario = new Usuario();
        $usuario->nombre = $_POST['nombre'] ?? '';
        $usuario->apellidoP = $_POST['apellidoP'] ?? '';
        $usuario->apellidoM = $_POST['apellidoM'] ?? '';
        $usuario->email = $_POST['email'] ?? '';
        $usuario->password = $_POST['password'] ?? '';
        $usuario->idTipoUsuario = 2; // Usuario normal por defecto
        $usuario->estatus = 'ACTIVO';

        // Validar
        $errors = $usuario->validate();

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            View::redirect('/register');
            return;
        }

        // Verificar que el email no exista
        $existente = Usuario::where('email', $usuario->email);
        if (!empty($existente)) {
            flash('error', 'El email ya está registrado');
            View::redirect('/register');
            return;
        }

        // Guardar
        try {
            if ($usuario->save()) {
                // Auto-login
                $usuario->startSession();
                flash('success', '¡Cuenta creada exitosamente!');
                View::redirect('/dashboard');
            } else {
                flash('error', 'Error al crear la cuenta');
                View::redirect('/register');
            }
        } catch (Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            flash('error', 'Error del servidor');
            View::redirect('/register');
        }
    }

    /**
     * CHANGE PASSWORD - Cambiar contraseña
     * 
     * Ruta: POST /change-password
     */
    public function changePassword()
    {
        $usuario = Usuario::current();

        if (!$usuario) {
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones
        if (empty($currentPassword) || empty($newPassword)) {
            View::json([
                'success' => false,
                'message' => 'Todos los campos son requeridos'
            ]);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            View::json([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ]);
            return;
        }

        if (strlen($newPassword) < 8) {
            View::json([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres'
            ]);
            return;
        }

        // Verificar contraseña actual
        $check = Usuario::login($usuario->email, $currentPassword);

        if (!$check) {
            View::json([
                'success' => false,
                'message' => 'Contraseña actual incorrecta'
            ]);
            return;
        }

        // Actualizar contraseña
        $usuario->password = password_hash($newPassword, PASSWORD_BCRYPT);

        if ($usuario->save()) {
            View::json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } else {
            View::json([
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ]);
        }
    }

    /**
     * LOG ACCESS - Registrar acceso en logs
     * 
     * Útil para auditoría y seguridad
     */
    private function logAccess($userId, $action)
    {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO access_logs (id_usuario, accion, ip, user_agent, fecha) 
                    VALUES (?, ?, ?, ?, NOW())",
                [
                    $userId,
                    $action,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]
            );
        } catch (Exception $e) {
            error_log("Error al registrar log: " . $e->getMessage());
        }
    }

    /**
     * REQUIRE AUTH - Middleware para proteger rutas
     * 
     * Usar en cualquier controlador que requiera autenticación
     */
    public static function requireAuth()
    {
        if (!Usuario::isAuthenticated()) {
            flash('error', 'Debes iniciar sesión');
            View::redirect('/login');
            exit;
        }
    }

    /**
     * REQUIRE PERMISSION - Middleware para verificar permisos
     * 
     * Uso: AuthController::requirePermission('usuarios.crear');
     */
    public static function requirePermission($permission)
    {
        self::requireAuth();

        $usuario = Usuario::current();

        if (!$usuario->hasPermission($permission)) {
            View::error(403, 'No tienes permiso para realizar esta acción');
            exit;
        }
    }
}
