<?php

namespace WAFWork\Core;

use Dotenv\Dotenv;

class Environment
{
    /**
     * The base path of the application
     *
     * @var string
     */
    protected $basePath;

    /**
     * Create a new Environment instance
     *
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Load the environment variables
     *
     * @return void
     */
    public function load()
    {
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable($this->basePath);
        
        try {
            $dotenv->load();
        } catch (\Exception $e) {
            // Fail silently if .env file doesn't exist
        }
        
        // Set default environment variables if not set
        $this->setDefaultEnvironmentVariables();
    }

    /**
     * Set default environment variables
     *
     * @return void
     */
    protected function setDefaultEnvironmentVariables()
    {
        // Set application environment
        if (!isset($_ENV['APP_ENV'])) {
            $_ENV['APP_ENV'] = 'production';
        }
        
        // Set application debug mode
        if (!isset($_ENV['APP_DEBUG'])) {
            $_ENV['APP_DEBUG'] = $_ENV['APP_ENV'] === 'development';
        }
    }
} 