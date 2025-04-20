<?php

namespace WAFWork\Http;

class Response
{
    /**
     * The response content
     *
     * @var string|array
     */
    protected $content;

    /**
     * The response status code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * The response headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Create a new Response instance
     *
     * @param string|array $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set the response content
     *
     * @param string|array $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        
        return $this;
    }

    /**
     * Set the response status code
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        
        return $this;
    }

    /**
     * Add a header to the response
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        
        return $this;
    }

    /**
     * Add multiple headers to the response
     *
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        
        return $this;
    }

    /**
     * Send the response
     *
     * @return void
     */
    public function send()
    {
        // Send the status code
        http_response_code($this->statusCode);
        
        // Send the headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        // Send the content
        if (is_array($this->content) || is_object($this->content)) {
            // Set the content type to JSON
            header('Content-Type: application/json');
            
            // Send the JSON response
            echo json_encode($this->content);
        } else {
            // Send the content
            echo $this->content;
        }
    }

    /**
     * Create a JSON response
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public static function json($data, $statusCode = 200, array $headers = [])
    {
        return new static($data, $statusCode, array_merge([
            'Content-Type' => 'application/json'
        ], $headers));
    }

    /**
     * Create a redirect response
     *
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public static function redirect($url, $statusCode = 302, array $headers = [])
    {
        return new static('', $statusCode, array_merge([
            'Location' => $url
        ], $headers));
    }

    /**
     * Create a view response
     *
     * @param string $view
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public static function view($view, $data = [], $statusCode = 200, array $headers = [])
    {
        // Render the view
        $content = app()->get('view')->render($view, $data);
        
        return new static($content, $statusCode, $headers);
    }
} 