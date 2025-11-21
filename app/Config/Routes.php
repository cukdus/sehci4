<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomePublic::index');
$routes->get('home/(:segment)', 'HomePublic::page/$1');
$routes->get('register', 'HomePublic::page/register');
