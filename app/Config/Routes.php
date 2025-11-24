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
$routes->get('admin/anggota', 'Admin::anggota');
$routes->get('admin/anggota/data', 'Admin::anggotaData');
$routes->get('admin/anggota/tambah', 'Admin::anggotaTambah');
$routes->get('admin/anggota/edit/(:num)', 'Admin::anggotaEdit/$1');
$routes->get('admin/anggota/lihat/(:num)', 'Admin::anggotaLihat/$1');
$routes->post('admin/anggota/create', 'Admin::createAnggota');
$routes->post('admin/anggota/update', 'Admin::updateAnggota');
$routes->post('admin/anggota/delete', 'Admin::deleteAnggota');
$routes->get('anggota', 'Anggota::index');
$routes->get('anggota/profil', 'Anggota::profil');
$routes->get('anggota/profil/edit', 'Anggota::profilEdit');
$routes->post('anggota/profil/update', 'Anggota::profilUpdate');
