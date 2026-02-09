<?php 

/**
 * DEFINICIÓN DE RUTAS con AJAX
 * 
 * Aquí defines todas las rutas que dependan de ajax
 */

//! =====================================================
//! RUTAS PÚBLICAS (Sin autenticación)
//! =====================================================



//! =====================================================
//! RUTAS PROTEGIDAS (Requieren autenticación)
//! =====================================================
if (Usuario::isAuthenticated()){
    //? =====================================================
    //* MÓDULO: USUARIOS
    //? =====================================================
    //* Buscar usuarios
    $router->get('/ajax/usuarios/buscar', [UserController::class, 'search']);
    //* Validar email único
    $router->get('/ajax/usuarios/validar-email', [UserController::class, 'validateEmail']);
    //* Obtener un usuario
    $router->get('/ajax/usuarios/{id}', [UserController::class, 'show']);
}