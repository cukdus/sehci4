<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomePublic::index');
$routes->get('home/(:segment)', 'HomePublic::page/$1');
$routes->get('register', 'HomePublic::page/register');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('admin', 'Admin::index');
$routes->get('admin/pinjaman', 'Admin::pinjaman');
$routes->post('admin/pinjaman/approve', 'Admin::approvePermohonan');
$routes->post('admin/pinjaman/reject', 'Admin::rejectPermohonan');
$routes->get('anggota', 'Anggota::index');
