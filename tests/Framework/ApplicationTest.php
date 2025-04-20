<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WAFWork\Core\Application;
use WAFWork\Core\Container;

class ApplicationTest extends TestCase
{
    public function testApplicationInstantiation()
    {
        $app = new Application();
        $this->assertInstanceOf(Application::class, $app);
    }
    
    public function testGetInstance()
    {
        $app = new Application();
        $instance = Application::getInstance();
        $this->assertSame($app, $instance);
    }
    
    public function testGetContainer()
    {
        $app = new Application();
        $container = $app->getContainer();
        $this->assertInstanceOf(Container::class, $container);
    }
    
    public function testGetRouter()
    {
        $app = new Application();
        $router = $app->getRouter();
        $this->assertInstanceOf(\WAFWork\Core\Router::class, $router);
    }
} 