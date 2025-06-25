<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\ProgressController as AdminProgressController;
use App\Http\Controllers\Admin\PekerjaanController as AdminPekerjaanController;
use App\Http\Controllers\Admin\MasterPegawaiController as AdminPegawaiController;

// SUPERADMIN CONTROLLERS
use App\Http\Controllers\Superadmin\ProgressController as SuperadminProgressController;
use App\Http\Controllers\Superadmin\PekerjaanController as SuperadminPekerjaanController;
use App\Http\Controllers\Superadmin\MasterPegawaiController as SuperadminPegawaiController;
use App\Http\Controllers\Superadmin\MasterUserController;
use App\Http\Controllers\Superadmin\MasterJenisPekerjaanController;
use App\Http\Controllers\Superadmin\MasterJenisTimController;

// =========================
// AUTH ROUTES
// =========================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================
// ADMIN PANEL ROUTES
// =========================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::get('/progress', [AdminProgressController::class, 'index'])->name('progress');
    Route::get('/pekerjaan', [AdminPekerjaanController::class, 'index'])->name('pekerjaan');
    Route::get('/pegawai', [AdminPegawaiController::class, 'index'])->name('masterpegawai');
});

// =========================
// SUPERADMIN PANEL ROUTES
// =========================
Route::middleware(['auth'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');

    Route::get('/progress', [SuperadminProgressController::class, 'index'])->name('progress');
    Route::get('/pekerjaan', [SuperadminPekerjaanController::class, 'index'])->name('pekerjaan');
    Route::get('/pegawai', [SuperadminPegawaiController::class, 'index'])->name('masterpegawai');

    // web.php (dalam group prefix 'superadmin', name 'superadmin.')
    Route::get('/masteruser', [MasterUserController::class, 'index'])->name('masteruser');
    Route::post('/masteruser', [MasterUserController::class, 'store'])->name('masteruser.store');
    Route::get('/masteruser/create', [MasterUserController::class, 'create'])->name('masteruser.create');
    Route::get('/masteruser/{id}/edit', [MasterUserController::class, 'edit'])->name('masteruser.edit');
    Route::put('/masteruser/{id}', [MasterUserController::class, 'update'])->name('masteruser.update');
    Route::delete('/masteruser/{id}', [MasterUserController::class, 'destroy'])->name('masteruser.destroy');

    // web.php (dalam group prefix 'superadmin', name 'superadmin.')
    Route::get('/masterjenistim', [MasterJenisTimController::class, 'index'])->name('masterjenistim');
    Route::post('/masterjenistim', [MasterJenisTimController::class, 'store'])->name('masterjenistim.store');
    Route::get('/masterjenistim/create', [MasterJenisTimController::class, 'create'])->name('masterjenistim.create');
    Route::get('/masterjenistim/{id}/edit', [MasterJenisTimController::class, 'edit'])->name('masterjenistim.edit');
    Route::put('/masterjenistim/{id}', [MasterJenisTimController::class, 'update'])->name('masterjenistim.update');
    Route::delete('/masterjenistim/{id}', [MasterJenisTimController::class, 'destroy'])->name('masterjenistim.destroy');

    // web.php (dalam group prefix 'superadmin', name 'superadmin.')
    Route::get('/masterjenispekerjaan', [MasterJenisPekerjaanController::class, 'index'])->name('masterjenispekerjaan');
    Route::post('/masterjenispekerjaan', [MasterJenisPekerjaanController::class, 'store'])->name('masterjenispekerjaan.store');
    Route::get('/masterjenispekerjaan/create', [MasterJenisPekerjaanController::class, 'create'])->name('masterjenispekerjaan.create');
    Route::get('/masterjenispekerjaan/{id}/edit', [MasterJenisPekerjaanController::class, 'edit'])->name('masterjenispekerjaan.edit');
    Route::put('/masterjenispekerjaan/{id}', [MasterJenisPekerjaanController::class, 'update'])->name('masterjenispekerjaan.update');
    Route::delete('/masterjenispekerjaan/{id}', [MasterJenisPekerjaanController::class, 'destroy'])->name('masterjenispekerjaan.destroy');

    Route::resource('/jenis-pekerjaan', MasterJenisPekerjaanController::class);
    Route::resource('/jenis-tim', MasterJenisTimController::class);
});
