<?php

/**
 * Helper Functions
 *
 * These functions provide shortcuts to common framework functionality.
 */

use WAFWork\Core\Application;
use WAFWork\Http\Response;

if (!function_exists('app')) {
    /**
     * Get the application instance or a service from the container
     *
     * @param string|null $abstract
     * @return mixed
     */
    function app($abstract = null) {
        $app = Application::getInstance();
        
        if ($abstract) {
            return $app->getContainer()->resolve($abstract);
        }
        
        return $app;
    }
}

if (!function_exists('view')) {
    /**
     * Render a view
     *
     * @param string $view
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    function view($view, $data = [], $status = 200, array $headers = []) {
        return Response::view($view, $data, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return Response
     */
    function redirect($url, $status = 302, array $headers = []) {
        return Response::redirect($url, $status, $headers);
    }
}

if (!function_exists('config')) {
    /**
     * Get a configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config($key, $default = null) {
        list($file, $key) = explode('.', $key, 2);
        
        $path = ROOT_PATH . '/config/' . $file . '.php';
        
        if (!file_exists($path)) {
            return $default;
        }
        
        $config = require $path;
        
        return $config[$key] ?? $default;
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('asset')) {
    /**
     * Get the URL for an asset
     *
     * @param string $path
     * @return string
     */
    function asset($path) {
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL for a route
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    function url($path, $params = []) {
        $baseUrl = rtrim(config('app.url'), '/');
        $path = '/' . ltrim($path, '/');
        
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }
        
        return $baseUrl . $path;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token
     *
     * @return string
     */
    function csrf_token() {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token field
     *
     * @return string
     */
    function csrf_field() {
        return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old($key, $default = null) {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate a method field for spoofing HTTP methods
     *
     * @param string $method
     * @return string
     */
    function method_field($method) {
        return '<input type="hidden" name="_method" value="' . $method . '">';
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with an error response
     *
     * @param int $code
     * @param string $message
     * @return void
     */
    function abort($code, $message = '') {
        $response = new Response($message, $code);
        $response->send();
        exit;
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class
     *
     * @param string|object $class
     * @return string
     */
    function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;
        
        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('pluralize')) {
    /**
     * Convert a word to its plural form
     *
     * @param string $word
     * @return string
     */
    function pluralize($word) {
        $plural = [
            '/(quiz)$/i' => "$1zes",
            '/^(ox)$/i' => "$1en",
            '/([m|l])ouse$/i' => "$1ice",
            '/(matr|vert|ind)ix|ex$/i' => "$1ices",
            '/(x|ch|ss|sh)$/i' => "$1es",
            '/([^aeiouy]|qu)y$/i' => "$1ies",
            '/(hive)$/i' => "$1s",
            '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
            '/(shea|lea|loa|thie)f$/i' => "$1ves",
            '/sis$/i' => "ses",
            '/([ti])um$/i' => "$1a",
            '/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
            '/(bu)s$/i' => "$1ses",
            '/(alias)$/i' => "$1es",
            '/(octop)us$/i' => "$1i",
            '/(ax|test)is$/i' => "$1es",
            '/(us)$/i' => "$1es",
            '/s$/i' => "s",
            '/$/' => "s"
        ];
        
        $irregular = [
            'move' => 'moves',
            'foot' => 'feet',
            'goose' => 'geese',
            'sex' => 'sexes',
            'child' => 'children',
            'man' => 'men',
            'tooth' => 'teeth',
            'person' => 'people'
        ];
        
        $uncountable = [
            'sheep',
            'fish',
            'deer',
            'series',
            'species',
            'money',
            'rice',
            'information',
            'equipment',
            'data'
        ];
        
        // Check if it's uncountable
        if (in_array(strtolower($word), $uncountable)) {
            return $word;
        }
        
        // Check for irregular forms
        foreach ($irregular as $pattern => $result) {
            $pattern = '/' . $pattern . '$/i';
            
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $result, $word);
            }
        }
        
        // Check for regular forms
        foreach ($plural as $pattern => $result) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $result, $word);
            }
        }
        
        return $word;
    }
} 