<?php

namespace WAFWork\Http;

class Request
{
    /**
     * The request method
     *
     * @var string
     */
    protected $method;

    /**
     * The request URI
     *
     * @var string
     */
    protected $uri;

    /**
     * The request query parameters
     *
     * @var array
     */
    protected $query = [];

    /**
     * The request body parameters
     *
     * @var array
     */
    protected $request = [];

    /**
     * The route parameters
     *
     * @var array
     */
    protected $routeParams = [];

    /**
     * Create a new Request instance
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->query = $_GET ?? [];
        $this->request = $_POST ?? [];
        
        // Handle PUT, PATCH, DELETE requests
        if ($this->isMethodOverridden()) {
            $this->parseRequestBody();
        }
    }

    /**
     * Check if the request method is overridden
     *
     * @return bool
     */
    protected function isMethodOverridden()
    {
        if (isset($_POST['_method'])) {
            $this->method = strtoupper($_POST['_method']);
            unset($_POST['_method']);
            $this->request = $_POST;
            return true;
        }
        
        return false;
    }

    /**
     * Parse the request body for PUT, PATCH, DELETE requests
     *
     * @return void
     */
    protected function parseRequestBody()
    {
        $input = file_get_contents('php://input');
        
        if ($input) {
            parse_str($input, $data);
            $this->request = array_merge($this->request, $data);
        }
    }

    /**
     * Get the request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the request URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get a query parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function query($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->query;
        }
        
        return $this->query[$key] ?? $default;
    }

    /**
     * Get a request parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request;
        }
        
        return $this->request[$key] ?? $default;
    }

    /**
     * Check if a request parameter exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->request[$key]);
    }

    /**
     * Get all input parameters
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * Set the route parameters
     *
     * @param array $params
     * @return void
     */
    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
    }

    /**
     * Get a route parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function param($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->routeParams;
        }
        
        return $this->routeParams[$key] ?? $default;
    }

    /**
     * Check if the request is an AJAX request
     *
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if the request is a JSON request
     *
     * @return bool
     */
    public function isJson()
    {
        return strpos($this->getContentType(), 'application/json') !== false;
    }

    /**
     * Get the request content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }
} 