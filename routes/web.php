<?php

/**
 * DEFINICIÓN DE RUTAS
 * 
 * Aquí defines todas las rutas de tu aplicación
 * Mantén este archivo organizado por secciones
 */

// =====================================================
// RUTAS PÚBLICAS (Sin autenticación)
// =====================================================

// Home / Login
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

// Logout
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

// =====================================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// =====================================================

if (Usuario::isAuthenticated()) {
    // Dashboard
    $router->get('/dashboard', function ($params) {

        $usuario = Usuario::current();

        View::render('pages/dashboard', [
            'title' => 'Dashboard',
            'usuario' => $usuario
        ]);
    });

    // =====================================================
    // MÓDULO: USUARIOS
    // =====================================================

    // Listar usuarios
    $router->get('/usuarios', function ($params) {
        $usuarios = Usuario::all();

        View::render('pages/usuarios/index', [
            'title' => 'Usuarios',
            'usuarios' => $usuarios
        ]);
    });

    // Crear usuario - Formulario
    $router->get('/usuarios/crear', function ($params) {

        View::render('pages/usuarios/form', [
            'title' => 'Crear Usuario',
            'usuario' => new Usuario(),
        ]);
    });

    // Editar usuario - Formulario
    $router->get('/usuarios/editar/{id}', function ($params) {

        //AuthController::requirePermission('usuarios.editar');

        $usuario = Usuario::find($params['id']);

        if (!$usuario) {
            flash('error', 'Usuario no encontrado');
            View::redirect('/usuarios');
            return;
        }

        View::render('pages/usuarios/form', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
        ]);
    });

    // Guardar usuario (crear o actualizar)
    $router->post('/usuarios/guardar', function ($params) {
        $id = $_POST['id'] ?? null;

        if ($id) {
            //AuthController::requirePermission('usuarios.editar');
            $usuario = Usuario::find($id);
            if (!$usuario) {
                flash('error', 'Usuario no encontrado');
                View::redirect('/usuarios');
                return;
            }
        } else {
            //AuthController::requirePermission('usuarios.crear');
            $usuario = new Usuario();
        }

        // Llenar datos
        $usuario->nombre = $_POST['nombre'];
        $usuario->apellidoP = $_POST['apellidoP'];
        $usuario->apellidoM = $_POST['apellidoM'];
        $usuario->email = $_POST['email'];

        //dd($usuario);

        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($_POST['password'])) {
            $usuario->password = $_POST['password'];
        }

        // Validar
        $errors = $usuario->validate();
        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            View::back();
            return;
        }

        // Guardar
        if ($usuario->save()) {
            flash('success', 'Usuario guardado correctamente');
            View::redirect('/usuarios');
        } else {
            flash('error', 'Error al guardar usuario');
            View::back();
        }
    });

    // Eliminar usuario
    $router->post('/usuarios/eliminar/{id}', function ($params) {
        //AuthController::requirePermission('usuarios.eliminar');

        $usuario = Usuario::find($params['id']);

        if (!$usuario) {
            View::json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            return;
        }

        // No permitir eliminar tu propio usuario
        if ($usuario->id == Usuario::current()->id) {
            View::json(['success' => false, 'message' => 'No puedes eliminarte a ti mismo'], 400);
            return;
        }

        if ($usuario->delete()) {
            View::json(['success' => true, 'message' => 'Usuario eliminado']);
        } else {
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    });

    // =====================================================
    // MÓDULO: ROLES (Ejemplo para que agregues más)
    // =====================================================

    $router->get('/roles/index', function ($params) {

        //  Implementar vista de roles
        echo "<h1>Módulo de Roles - En construcción</h1>";
        echo '<a href="' . url('dashboard') . '">Volver</a>';
    });

    // =====================================================
    // MÓDULO: CLIENTES (Ejemplo)
    // =====================================================

    $router->get('/clientes/index', function ($params) {

        // : Implementar vista de clientes
        echo "<h1>Módulo de Clientes - En construcción</h1>";
        echo '<a href="' . url('dashboard') . '">Volver</a>';
    });
}

// =====================================================
// Ruta 404
// =====================================================
$router->notFound(function () {
    View::error(404, 'Página no encontrada');
});
