<?php
/**
 * Clase Router - Sistema de Enrutamiento
 * 
 * Maneja todas las rutas de la aplicación y ejecuta
 * los controladores correspondientes.
 */
class Router {
    private $routes = [];
    private $notFoundCallback;
    private $currentRoute = null;

    /**
     * Registrar ruta GET
     */
    public function get($pattern, $callback) {
        $this->routes['GET'][$pattern] = $callback;
        return $this;
    }

    /**
     * Registrar ruta POST
     */
    public function post($pattern, $callback) {
        $this->routes['POST'][$pattern] = $callback;
        return $this;
    }

    /**
     * Registrar ruta DELETE
     */
    public function delete($pattern, $callback) {
        $this->routes['DELETE'][$pattern] = $callback;
        return $this;
    }

    /**
     * Registrar ruta PUT
     */
    public function put($pattern, $callback) {
        $this->routes['PUT'][$pattern] = $callback;
        return $this;
    }

    /**
     * Registrar callback para 404
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
        return $this;
    }

    /**
     * Ejecutar el router
     */
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_GET['url'] ?? '';
        $url = '/' . trim($url, '/');

        // Si es DELETE o PUT desde formulario
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Buscar ruta coincidente
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $callback) {
                $patternRegex = $this->convertPatternToRegex($pattern);
                
                if (preg_match($patternRegex, $url, $matches)) {
                    // Filtrar solo parámetros nombrados
                    $params = array_filter($matches, function($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);

                    // Guardar ruta actual
                    $this->currentRoute = [
                        'pattern' => $pattern,
                        'params' => $params,
                        'method' => $method
                    ];

                    // Ejecutar callback
                    return $this->executeCallback($callback, $params);
                }
            }
        }

        // Si no se encontró, ejecutar 404
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        } else {
            $this->defaultNotFound($url);
        }
    }

    /**
     * Convertir patrón a regex
     */
    private function convertPatternToRegex($pattern) {
        $pattern = preg_replace('#[.\\+*?\\[^\\]$(){}=!<>|:\\-]#', '\\\\$0', $pattern);
        $pattern = preg_replace_callback(
            '/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}/',
            function($matches) {
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $pattern
        );
        return '#^' . $pattern . '$#';
    }

    /**
     * Ejecutar callback
     */
    private function executeCallback($callback, $params) {
        if (is_array($callback)) {
            list($controller, $method) = $callback;
            
            if (is_string($controller)) {
                $controller = new $controller();
            }

            return call_user_func([$controller, $method], $params);
        } elseif (is_callable($callback)) {
            return call_user_func($callback, $params);
        }
    }

    /**
     * 404 por defecto
     */
    private function defaultNotFound($url) {
        View::error(404, 'Página no encontrada');
    }

    /**
     * Obtener ruta actual
     */
    public function getCurrentRoute() {
        return $this->currentRoute;
    }
}