<?php

/**
 * WAFWork - Minimal PHP MVC Framework
 * 
 * This is the entry point for the application.
 * All requests are routed through this file.
 */

// Define the application root directory
define('ROOT_PATH', dirname(__DIR__));

// Load the autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
(new \WAFWork\Core\Environment(ROOT_PATH))->load();

// Initialize the application
$app = new \WAFWork\Core\Application();

// Load the routes
require_once ROOT_PATH . '/routes/web.php';

// Run the application
$app->run(); 