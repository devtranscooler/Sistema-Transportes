<?php
/**
 * Sistema de Vistas y Componentes
 * 
 * Esta clase permite renderizar vistas y componentes reutilizables
 * de forma simple y organizada.
 * 
 */

class View {
    private static $basePath = __DIR__ . '/../views/';
    private static $layoutPath = null;
    
    /**
     * RENDER - Renderizar una vista completa
     * 
     * Uso: View::render('pages/dashboard', ['title' => 'Dashboard']);
     * 
     * @param string $view Ruta de la vista (sin .php)
     * @param array $data Datos a pasar a la vista
     * @param string $layout Layout a usar (opcional)
     */
    public static function render($view, $data = [], $layout = 'main') {
        // Extraer variables para usarlas en la vista
        extract($data);
        
        // Capturar el contenido de la vista
        ob_start();
        $viewPath = self::$basePath . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: $viewPath");
        }
        
        include $viewPath;
        $content = ob_get_clean();
        
        // Si hay layout, renderizar con layout
        if ($layout) {
            $layoutPath = self::$basePath . 'layouts/' . $layout . '.php';
            
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content; // Sin layout
            }
        } else {
            echo $content;
        }
    }

    /**
     * COMPONENT - Renderizar componente reutilizable
     * 
     * Los componentes son piezas de UI que usas múltiples veces:
     * botones, inputs, cards, modales, etc.
     * 
     * Uso: View::component('forms/input', ['name' => 'email', 'label' => 'Email']);
     * 
     * @param string $component Nombre del componente
     * @param array $props Propiedades del componente
     * @return string HTML del componente
     */
    public static function component($component, $props = []) {
        extract($props);
        
        ob_start();
        $componentPath = self::$basePath . 'components/' . $component . '.php';
        
        if (!file_exists($componentPath)) {
            throw new Exception("Componente no encontrado: $componentPath");
        }
        
        include $componentPath;
        return ob_get_clean();
    }

    /**
     * PARTIAL - Renderizar vista parcial
     * 
     * Similar a render pero sin layout
     */
    public static function partial($view, $data = []) {
        extract($data);
        
        $viewPath = self::$basePath . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Parcial no encontrada: $viewPath");
        }
        
        include $viewPath;
    }

    /**
     * JSON - Retornar respuesta JSON
     * 
     * Útil para APIs o respuestas AJAX
     */
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * REDIRECT - Redirigir a otra página
     */
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }

    /**
     * BACK - Volver a la página anterior
     */
    public static function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    /**
     * ERROR - Mostrar página de error
     */
    public static function error($code = 404, $message = null) {
        http_response_code($code);
        
        $messages = [
            404 => 'Pagina no encontrada',
            403 => 'Acceso denegado',
            500 => 'Error interno del servidor'
        ];
        
        $message = $message ?? $messages[$code] ?? 'Error';
        
        self::render("errors/{$code}" , [
            'code' => $code,
            'message' => $message
        ], 'error');
    }
}