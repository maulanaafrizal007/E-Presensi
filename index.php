<?php
/**
 * SIKAP DPD RI - Standalone Entry Point
 * Tidak memerlukan Composer / vendor folder.
 * Letakkan folder E-Presensi di htdocs, akses: http://localhost/E-Presensi
 */

define('BASE_PATH', __DIR__);
define('BASE_URL',  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// Muat semua modul
require BASE_PATH . '/app/core/Database.php';
require BASE_PATH . '/app/core/Auth.php';
require BASE_PATH . '/app/core/Router.php';
require BASE_PATH . '/app/core/Helpers.php';
require BASE_PATH . '/app/controllers/AuthController.php';
require BASE_PATH . '/app/controllers/DashboardController.php';
require BASE_PATH . '/app/controllers/PengaduanController.php';
require BASE_PATH . '/app/controllers/ProfilController.php';

// Mulai session
session_start();

// Routing
$router = new Router();

// --- Auth ---
$router->get('/',          'AuthController@redirectHome');
$router->get('/login',     'AuthController@showLogin');
$router->post('/login',    'AuthController@login');
$router->post('/logout',   'AuthController@logout');

// --- Dashboard ---
$router->get('/dashboard', 'DashboardController@index');

// --- Pengaduan ---
$router->get('/pengaduan',              'PengaduanController@index');
$router->get('/pengaduan/buat',         'PengaduanController@create');
$router->post('/pengaduan/buat',        'PengaduanController@store');
$router->get('/pengaduan/{id}',         'PengaduanController@show');
$router->post('/pengaduan/{id}/respons','PengaduanController@respond');
$router->post('/pengaduan/{id}/verifikasi','PengaduanController@storeVerifikasi');

// --- Profil ---
$router->get('/profil',                   'ProfilController@index');
$router->get('/profil/pengguna',          'ProfilController@users');
$router->get('/profil/pengguna/buat',     'ProfilController@createUser');
$router->post('/profil/pengguna/buat',    'ProfilController@storeUser');
$router->get('/profil/pengguna/{id}/edit','ProfilController@editUser');
$router->post('/profil/pengguna/{id}/edit','ProfilController@updateUser');
$router->post('/profil/pengguna/{id}/toggle','ProfilController@toggleUser');

$router->dispatch();
