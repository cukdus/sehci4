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
$routes->get('admin/anggota/lihatsimpan/(:num)', 'Admin::anggotaLihatSimpan/$1');
$routes->get('admin/anggota/lihatpinjam/(:num)', 'Admin::anggotaLihatPinjam/$1');
$routes->get('admin/api/anggota/(:num)/simpanan', 'Admin::apiSimpananAnggota/$1');
$routes->get('admin/api/anggota/(:num)/simpanan/summary', 'Admin::apiSimpananAnggotaSummary/$1');
$routes->post('admin/anggota/create', 'Admin::createAnggota');
$routes->post('admin/anggota/update', 'Admin::updateAnggota');
$routes->post('admin/anggota/delete', 'Admin::deleteAnggota');
$routes->get('anggota', 'Anggota::index');
$routes->get('anggota/profil', 'Anggota::profil');
$routes->get('anggota/profil/edit', 'Anggota::profilEdit');
$routes->post('anggota/profil/update', 'Anggota::profilUpdate');
$routes->post('anggota/profil/berhenti', 'Anggota::permohonanBerhenti');
$routes->get('anggota/simpanan/wajib', 'Anggota::simpananWajib');
$routes->get('anggota/simpanan/sukarela', 'Anggota::simpananSukarela');
$routes->get('anggota/simpanan/sukarela/tambah', 'Anggota::simpananSukarelaTambah');
$routes->get('anggota/simpanan/hibah', 'Anggota::hibah');
$routes->get('anggota/simpanan/data', 'Anggota::dataSimpanan');
$routes->get('anggota/api/simpanan/data', 'Anggota::apiSimpananData');
$routes->get('anggota/api/simpanan/summary', 'Anggota::apiSimpananSummary');
$routes->get('anggota/api/pinjaman', 'Anggota::apiPinjamanData');
$routes->post('anggota/simpanan/wajib/tambah', 'Anggota::tambahWajib');
$routes->get('anggota/api/simpanan/wajib', 'Anggota::apiSimpananWajib');
$routes->get('anggota/api/simpanan/sukarela', 'Anggota::apiSimpananSukarela');
$routes->post('anggota/simpanan/sukarela/tambah', 'Anggota::tambahSukarela');
$routes->get('anggota/api/simpanan/hibah', 'Anggota::apiHibah');
$routes->get('admin/anggota/berhenti', 'Admin::anggotaBerhenti');
$routes->post('admin/anggota/berhenti/approve', 'Admin::approveBerhenti');
$routes->post('admin/anggota/berhenti/reject', 'Admin::rejectBerhenti');

// Admin Simpanan
$routes->get('admin/simpanan/pokok', 'Admin::simpananPokok');
$routes->get('admin/simpanan/wajib', 'Admin::simpananWajib');
$routes->get('admin/simpanan/sukarela', 'Admin::simpananSukarela');
$routes->get('admin/simpanan/data', 'Admin::simpananData');
$routes->get('admin/api/simpanan/pokok', 'Admin::apiSimpananPokok');
$routes->get('admin/api/simpanan/wajib', 'Admin::apiSimpananWajib');
$routes->get('admin/api/simpanan/sukarela', 'Admin::apiSimpananSukarela');
$routes->get('admin/api/simpanan/data', 'Admin::apiSimpananData');
$routes->get('admin/api/simpanan/summary', 'Admin::apiSimpananSummary');
$routes->post('admin/simpanan/sukarela/activate', 'Admin::activateSukarela');
