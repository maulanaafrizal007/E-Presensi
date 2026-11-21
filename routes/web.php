<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIKAP DPD RI
| Three main navigation menus: Dashboard | Pengaduan Presensi | Profil
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC ROUTES (unauthenticated)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    // Root redirect
    Route::get('/', fn() => redirect()->route('dashboard'));

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PENGADUAN PRESENSI
    Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
        // List (pegawai: own list; admin/manajemen: all)
        Route::get('/', [PengaduanController::class, 'index'])->name('index');

        // Create & store (pegawai only)
        Route::get('/buat', [PengaduanController::class, 'create'])
            ->middleware('check.role:pegawai')
            ->name('create');
        Route::post('/', [PengaduanController::class, 'store'])
            ->middleware('check.role:pegawai')
            ->name('store');

        // Show detail (all roles, with access control inside controller)
        Route::get('/{pengaduan}', [PengaduanController::class, 'show'])->name('show');

        // Admin: add response / update status
        Route::post('/{pengaduan}/respons', [PengaduanController::class, 'respond'])
            ->middleware('check.role:admin')
            ->name('respond');

        // Admin: manual verification
        Route::post('/{pengaduan}/verifikasi', [PengaduanController::class, 'storeVerifikasi'])
            ->middleware('check.role:admin')
            ->name('verifikasi.store');
    });

    // 3. PROFIL
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/', [ProfilController::class, 'index'])->name('index');

        // Admin: user management (within Profil menu)
        Route::get('/pengguna', [ProfilController::class, 'users'])
            ->middleware('check.role:admin')
            ->name('users');
        Route::get('/pengguna/buat', [ProfilController::class, 'createUser'])
            ->middleware('check.role:admin')
            ->name('users.create');
        Route::post('/pengguna', [ProfilController::class, 'storeUser'])
            ->middleware('check.role:admin')
            ->name('users.store');
        Route::get('/pengguna/{user}/edit', [ProfilController::class, 'editUser'])
            ->middleware('check.role:admin')
            ->name('users.edit');
        Route::put('/pengguna/{user}', [ProfilController::class, 'updateUser'])
            ->middleware('check.role:admin')
            ->name('users.update');
        Route::post('/pengguna/{user}/toggle-status', [ProfilController::class, 'toggleUserStatus'])
            ->middleware('check.role:admin')
            ->name('users.toggle-status');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
