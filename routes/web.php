<?php

/**
 * DEFINICIÓN DE RUTAS
 * 
 * Aquí defines todas las rutas de tu aplicación
 * Mantén este archivo organizado por secciones
 */

//! =====================================================
//! RUTAS PÚBLICAS (Sin autenticación)
//! =====================================================

// Home / Login
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

// Logout
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

//! =====================================================
//! RUTAS PROTEGIDAS (Requieren autenticación)
//! =====================================================

if (Usuario::isAuthenticated()) {
    // Dashboard
    $router->get('/dashboard', function ($params) {

        $usuario = Usuario::current();

        View::render('pages/dashboard', [
            'title' => 'Dashboard',
            'usuario' => $usuario
        ]);
    });

    //? =====================================================
    //* MÓDULO: USUARIOS
    //? =====================================================
    // Vista principal (HTML)
    $router->get('/usuarios', [UserController::class, 'index']);
    // Guardar (crear o actualizar)
    $router->post('/usuarios/guardar', [UserController::class, 'save']);
    // Eliminar
    $router->delete('/usuarios/eliminar/{id}', [UserController::class, 'delete']);
    // Exportar CSV
    $router->get('/usuarios/exportar', [UserController::class, 'export']);

    // =====================================================
    // MÓDULO: ROLES 
    // =====================================================
    $router->get('/roles', [RoleController::class, 'index']);

    // =====================================================
    // MÓDULO: CLIENTES (Ejemplo)
    // =====================================================

    $router->get('/clientes/index', function ($params) {
        // vista de clientes
        echo "<h1>Módulo de Clientes - En construcción</h1>";
        echo '<a href="' . url('dashboard') . '">Volver</a>';
    });
}


