<?php

/**
 * Web Routes
 *
 * Here is where you can register web routes for your application.
 */

$router = app()->getRouter();

// Home route
$router->get('/', 'HomeController@index');

// Auth routes
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegisterForm');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');

// User routes
$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@store');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@destroy'); 