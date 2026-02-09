<?php

/**
 * ========================================
 * AJAX RESPONSE - Respuestas AJAX 
 * ========================================
 */

class AjaxResponse
{

    /**
     * SUCCESS - Respuesta exitosa
     * 
     * Uso: AjaxResponse::success('Usuario creado', ['id' => 5]);
     */
    public static function success($message = 'Operación exitosa', $data = null, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'success' => true,
            'message' => $message
        ];

        // Si hay datos, agregarlos
        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ERROR - Respuesta de error
     * 
     * Uso: AjaxResponse::error('Usuario no encontrado', 404);
     */
    public static function error($message = 'Error en la operación', $statusCode = 400, $errors = null)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'message' => $message
        ];

        // Si hay errores específicos (validación), agregarlos
        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * VALIDATION ERROR - Error de validación
     * 
     * Uso: 
     *   AjaxResponse::validationError([
     *       'email' => 'El email es inválido',
     *       'password' => 'La contraseña debe tener mínimo 6 caracteres'
     *   ]);
     */
    public static function validationError($errors, $message = 'Errores de validación')
    {
        self::error($message, 422, $errors);
    }

    /**
     * NOT FOUND - Recurso no encontrado
     * 
     * Uso: AjaxResponse::notFound('Usuario no encontrado');
     */
    public static function notFound($message = 'Recurso no encontrado')
    {
        self::error($message, 404);
    }

    /**
     * UNAUTHORIZED - No autorizado
     * 
     * Uso: AjaxResponse::unauthorized('Necesitas iniciar sesión');
     */
    public static function unauthorized($message = 'No autorizado')
    {
        self::error($message, 401);
    }

    /**
     * FORBIDDEN - Prohibido
     * 
     * Uso: AjaxResponse::forbidden('No tienes permisos para esta acción');
     */
    public static function forbidden($message = 'Acceso prohibido')
    {
        self::error($message, 403);
    }

    /**
     * CREATED - Recurso creado
     * 
     * Uso: AjaxResponse::created('Usuario creado correctamente', ['id' => 5]);
     */
    public static function created($message = 'Recurso creado', $data = null)
    {
        self::success($message, $data, 201);
    }

    /**
     * DELETED - Recurso eliminado
     * 
     * Uso: AjaxResponse::deleted('Usuario eliminado correctamente');
     */
    public static function deleted($message = 'Recurso eliminado')
    {
        self::success($message, null, 200);
    }

    /**
     * NO CONTENT - Sin contenido (típico para DELETE)
     * 
     * Uso: AjaxResponse::noContent();
     */
    public static function noContent()
    {
        http_response_code(204);
        exit;
    }

    /**
     * REDIRECT - Redirigir desde AJAX
     * 
     * Uso: AjaxResponse::redirect('/dashboard');
     */
    public static function redirect($url, $message = null)
    {
        $response = [
            'success' => true,
            'redirect' => $url
        ];

        if ($message) {
            $response['message'] = $message;
        }

        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * DATA - Solo enviar datos
     * 
     * Uso: AjaxResponse::data(['usuarios' => $usuarios]);
     */
    public static function data($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * PAGINATED - Datos paginados
     * 
     * Uso: 
     *   AjaxResponse::paginated($usuarios, 50, 1, 10);
     */
    public static function paginated($data, $total, $currentPage = 1, $perPage = 10)
    {
        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'last_page' => ceil($total / $perPage),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $total)
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * IS AJAX - Verificar si es petición AJAX
     * 
     * Uso: 
     *   if (AjaxResponse::isAjax()) {
     *       // Manejar como AJAX
     *   }
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * REQUIRE AJAX - Forzar que la petición sea AJAX
     * 
     * Uso: AjaxResponse::requireAjax();
     */
    public static function requireAjax()
    {
        if (!self::isAjax()) {
            self::error('Esta ruta solo acepta peticiones AJAX', 400);
        }
    }
}
