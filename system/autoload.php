<?php
/**
 * AUTOLOAD DE CLASES
 * 
 * Este archivo se encarga de cargar automáticamente 
 * las clases cuando las necesitas.
 * 
 */

// ==========================================
// system/autoload.php - Autocargar clases
// ==========================================
spl_autoload_register(function ($className) {
    // Directorios donde buscar clases
    $directories = [
        BASE_PATH . '/core/',
        BASE_PATH . '/models/',
        BASE_PATH . '/controllers/',
        BASE_PATH . '/config/'
    ];

    // Buscar en cada directorio
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Si no se encuentra, lanzar error
    throw new Exception("Clase no encontrada: $className");
});

// Cargar helpers
require_once BASE_PATH . '/system/helpers.php';

// Cargar configuración de base de datos si existe
$configFile = BASE_PATH . '/config/database.php';
if (file_exists($configFile)) {
    require_once $configFile;
} 