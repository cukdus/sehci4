<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomePublic::index');
$routes->get('home/(:segment)', 'HomePublic::page/$1');
$routes->get('register', 'HomePublic::page/register');
$routes->group('', ['namespace' => 'Myth\Auth\Controllers'], static function ($routes) {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('logout', 'AuthController::logout');
});
$routes->get('admin', 'Admin::index');
