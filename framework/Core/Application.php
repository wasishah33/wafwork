<?php

namespace WAFWork\Core;

use WAFWork\Http\Request;
use WAFWork\Http\Response;
use WAFWork\Core\Router;

class Application
{
    /**
     * The application instance
     *
     * @var Application
     */
    protected static $instance;

    /**
     * The router instance
     *
     * @var Router
     */
    protected $router;

    /**
     * The service container
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new Application instance
     */
    public function __construct()
    {
        self::$instance = $this;
        
        $this->router = new Router();
        $this->container = new Container();
        
        $this->registerCoreServices();
    }

    /**
     * Register core services with the container
     */
    protected function registerCoreServices()
    {
        $this->container->bind('router', function () {
            return $this->router;
        });
        
        $this->container->bind('request', function () {
            return new Request();
        });
        
        $this->container->bind('response', function () {
            return new Response();
        });
    }

    /**
     * Get the application instance
     *
     * @return Application
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Get the service container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the router instance
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Run the application
     */
    public function run()
    {
        $request = $this->container->resolve('request');
        $response = $this->router->dispatch($request);
        
        $response->send();
    }
} 