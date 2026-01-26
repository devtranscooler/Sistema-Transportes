<?php
/**
 * =====================================================
 * PUNTO DE ENTRADA DE LA APLICACIÓN
 * =====================================================
 * 
 * Este archivo es el corazón de tu app.
 * Todas las peticiones llegan aquí gracias al .htaccess
 */

// Iniciar sesión
session_start();

// Configurar errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir constantes
define('BASE_PATH', __DIR__);
//define('APP_ENV', 'development'); 'production' en servidor

// Cargar autoload y helpers
require_once BASE_PATH . '/system/autoload.php';

// Crear instancia del router
$router = new Router();

// Cargar definición de rutas (web.php)
require_once BASE_PATH . '/routes/web.php';

// Ejecutar el router
try {
    $router->run();
} catch (Exception $e) {
    // Manejo de errores
    View::error(505, $e->getMessage());
}
