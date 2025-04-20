<?php

namespace WAFWork\Core;

use WAFWork\Http\Request;
use WAFWork\Http\Response;
use Exception;

class Router
{
    /**
     * All registered routes
     *
     * @var array
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => []
    ];

    /**
     * Route middleware groups
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * Register a GET route
     *
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route
     *
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route
     *
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route
     *
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a PATCH route
     *
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add a route to the router
     *
     * @param string $method
     * @param string $uri
     * @param string|callable $action
     * @return void
     */
    protected function addRoute($method, $uri, $action)
    {
        // Convert URL patterns to regex patterns
        $pattern = $this->convertUriToRegex($uri);
        
        $this->routes[$method][$pattern] = [
            'uri' => $uri,
            'action' => $action,
            'middleware' => []
        ];
    }

    /**
     * Convert a URI pattern to a regex pattern
     *
     * @param string $uri
     * @return string
     */
    protected function convertUriToRegex($uri)
    {
        // Convert URI parameters to regex patterns
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        
        // Prepare the pattern for full matching
        $pattern = '#^' . $pattern . '$#';
        
        return $pattern;
    }

    /**
     * Dispatch the request to the appropriate route
     *
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Find matching route
        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                return $this->handleRoute($route, $matches, $request);
            }
        }
        
        // No route found
        return $this->handleNotFound();
    }

    /**
     * Handle the matched route
     *
     * @param array $route
     * @param array $matches
     * @param Request $request
     * @return Response
     */
    protected function handleRoute($route, $matches, $request)
    {
        // Extract route parameters
        $params = array_filter($matches, function($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
        
        // Set route parameters
        $request->setRouteParams($params);
        
        // Get the route action
        $action = $route['action'];
        
        // Execute middleware
        foreach ($route['middleware'] as $middleware) {
            $middlewareInstance = new $middleware();
            $response = $middlewareInstance->handle($request, function($request) {
                return null; // Continue to next middleware
            });
            
            if ($response instanceof Response) {
                return $response;
            }
        }
        
        // Handle the action
        return $this->executeAction($action, $request);
    }

    /**
     * Execute the route action
     *
     * @param string|callable $action
     * @param Request $request
     * @return Response
     */
    protected function executeAction($action, $request)
    {
        if (is_callable($action)) {
            // Action is a Closure
            $response = call_user_func($action, $request);
        } elseif (is_string($action)) {
            // Action is a Controller@method string
            list($controller, $method) = explode('@', $action);
            
            // Add namespace if not present
            if (strpos($controller, '\\') === false) {
                $controller = "App\\Controllers\\{$controller}";
            }
            
            // Create controller instance
            $controllerInstance = new $controller();
            
            // Call controller method
            $response = call_user_func_array([$controllerInstance, $method], [$request]);
        } else {
            throw new Exception('Invalid route action.');
        }
        
        // If the response is not a Response object, create one
        if (!($response instanceof Response)) {
            $response = new Response($response);
        }
        
        return $response;
    }

    /**
     * Handle a 404 Not Found error
     *
     * @return Response
     */
    protected function handleNotFound()
    {
        return new Response('404 Not Found', 404);
    }

    /**
     * Add middleware to a route
     *
     * @param string $pattern
     * @param string $middleware
     * @return void
     */
    public function addMiddleware($pattern, $middleware)
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $routePattern => $route) {
                if ($route['uri'] === $pattern) {
                    $this->routes[$method][$routePattern]['middleware'][] = $middleware;
                }
            }
        }
    }
} 