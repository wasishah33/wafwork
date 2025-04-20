<?php

namespace WAFWork\Http;

use WAFWork\Core\Application;
use WAFWork\View\View;

abstract class Controller
{
    /**
     * The application instance
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->app = Application::getInstance();
    }

    /**
     * Render a view
     *
     * @param string $view
     * @param array $data
     * @return Response
     */
    protected function view($view, $data = [])
    {
        return Response::view($view, $data);
    }

    /**
     * Create a JSON response
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    protected function json($data, $statusCode = 200, array $headers = [])
    {
        return Response::json($data, $statusCode, $headers);
    }

    /**
     * Create a redirect response
     *
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    protected function redirect($url, $statusCode = 302, array $headers = [])
    {
        return Response::redirect($url, $statusCode, $headers);
    }

    /**
     * Get a validator instance
     *
     * @param array $data
     * @param array $rules
     * @return Validator
     */
    protected function validate($data, $rules)
    {
        return $this->app->getContainer()->resolve('validator')->make($data, $rules);
    }
} 