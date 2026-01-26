<?php
// ==========================================
// system/helpers.php - Funciones auxiliares
// ==========================================
?>
<?php
/**
 * FUNCIONES HELPER
 * 
 * Funciones útiles que puedes usar en cualquier parte de la app
 */

/**
 * DD - Dump and Die (para debugging)
 * 
 * Uso: dd($variable); // Imprime y detiene la ejecución
 */
function dd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * DUMP - Solo imprimir sin detener
 */
function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

/**
 * ENV - Obtener variable de entorno
 * 
 * Uso: env('DB_HOST', 'localhost');
 */
function env($key, $default = null)
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

/**
 * CONFIG - Obtener configuración
 * 
 * Uso: config('database.host');
 */
function config($key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $configFile = BASE_PATH . '/config/app.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
        } else {
            $config = [];
        }
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }

    return $value;
}

/**
 * SANITIZE - Limpiar entrada de usuario
 * 
 * Uso: $nombre = sanitize($_POST['nombre']);
 */
function sanitize($input)
{
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }

    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF TOKEN - Generar token CSRF
 */
function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF FIELD - Campo oculto con token CSRF
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * VERIFY CSRF - Verificar token CSRF
 */
function verify_csrf($token)
{
    return isset($_SESSION['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * REDIRECT - Redirigir con parámetros
 */
function redirect($url, $params = [])
{
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header("Location: $url");
    exit;
}

/**
 * IS POST - Verificar si es petición POST
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * IS GET - Verificar si es petición GET
 */
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * IS AJAX - Verificar si es petición AJAX
 */
function is_ajax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * FORMAT DATE - Formatear fecha en español
 * 
 * Uso: format_date('2024-01-15') // "15 de enero de 2024"
 */
function format_date($date, $format = 'long')
{
    $months = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);

    if ($format === 'long') {
        return "$day de $month de $year";
    } else {
        return "$day/$month/$year";
    }
}

/**
 * MONEY FORMAT - Formatear cantidad de dinero
 * 
 * Uso: money_format(1234.56) // "$1,234.56"
 */
function money_format_custom($amount, $currency = '$')
{
    return $currency . number_format($amount, 2, '.', ',');
}

/**
 * TRUNCATE - Truncar texto
 * 
 * Uso: truncate('Texto muy largo...', 20) // "Texto muy largo..."
 */
function truncate($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . $suffix;
}

/**
 * PLURALIZE - Pluralizar palabra
 * 
 * Uso: pluralize(5, 'usuario') // "5 usuarios"
 */
function pluralize($count, $singular, $plural = null)
{
    if ($plural === null) {
        $plural = $singular . 's';
    }

    return $count . ' ' . ($count === 1 ? $singular : $plural);
}

/**
 * TIME AGO - Tiempo transcurrido
 * 
 * Uso: time_ago('2024-01-01 10:00:00') // "hace 2 horas"
 */
function time_ago($datetime)
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'hace un momento';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "hace $minutes " . ($minutes === 1 ? 'minuto' : 'minutos');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "hace $hours " . ($hours === 1 ? 'hora' : 'horas');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "hace $days " . ($days === 1 ? 'día' : 'días');
    } else {
        return format_date($datetime);
    }
}

/**
 * GENERATE PASSWORD - Generar contraseña aleatoria
 */
function generate_password($length = 12)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}

/**
 * LOG ACTIVITY - Registrar actividad
 */
function log_activity($message, $level = 'info')
{
    $logFile = BASE_PATH . '/storage/logs/app.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] [$level] $message" . PHP_EOL;

    file_put_contents($logFile, $log, FILE_APPEND);
}

/**
 * Helper function para componentes
 * 
 * Uso: component('button', ['text' => 'Guardar', 'color' => 'primary']);
 */
function component($name, $props = [])
{
    return View::component($name, $props);
}

/**
 * Helper function para assets
 * 
 * Uso: asset('css/style.css') retorna '/public/assets/css/style.css'
 */
function asset($path)
{
    return '/' . ltrim($path, '/');
}

/**
 * Helper function para URLs
 * 
 * Uso: url('usuarios/crear') retorna '/usuarios/crear'
 */
function url($path = '')
{
    $baseUrl = rtrim($_SERVER['HTTP_HOST'], '/');
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Helper function para valores antiguos (útil en formularios)
 */
function old($key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Helper function para mensajes flash
 */
function flash($key, $message = null)
{
    if ($message === null) {
        // Obtener mensaje
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    } else {
        // Guardar mensaje
        $_SESSION['flash'][$key] = $message;
    }
}
