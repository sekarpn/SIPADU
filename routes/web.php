<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    PublicController,
    DashboardController,
    BookingController,
    AdminController
};

Route::get('/', [PublicController::class, 'landing'])->name('landing');

// AUTH
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ROUTE PEMINJAM
Route::middleware(['auth', 'role:peminjam'])
    ->prefix('peminjam')
    ->name('borrower.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'borrower'])->name('dashboard');

        Route::get('/pengajuan', [BookingController::class, 'index'])->name('bookings');
        Route::get('/ajukan', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/ajukan', [BookingController::class, 'store'])->name('booking.store');
        Route::patch('/pengajuan/{booking}/batal', [BookingController::class, 'cancel'])->name('booking.cancel');

        // DOWNLOAD TEMPLATE SURAT PENGAJUAN (SUDAH DIPERBAIKI)
        Route::get('/template-surat', [BookingController::class, 'downloadTemplateSurat'])
            ->name('booking.template');

        Route::get('/panduan', [DashboardController::class, 'guides'])->name('guides');

        // PROFIL PEMINJAM
        Route::get('/profil', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profil', [DashboardController::class, 'updateProfile'])->name('profile.update');

        // UBAH PASSWORD PEMINJAM
        Route::put('/profil/password', [DashboardController::class, 'updatePassword'])
            ->name('profile.password');
    });

// ROUTE ADMIN
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');

        // PENGELOLAAN PENGAJUAN
        Route::get('/pengajuan', [BookingController::class, 'adminIndex'])->name('bookings');

        // DOWNLOAD SURAT PEMINJAMAN
        Route::get('/pengajuan/{booking}/surat', [BookingController::class, 'downloadLetter'])
            ->name('booking.letter');

        // KEPUTUSAN PENGAJUAN
        Route::patch('/pengajuan/{booking}/keputusan', [BookingController::class, 'decide'])
            ->name('booking.decide');

        // KONTEN INFORMASI
        Route::get('/konten', [AdminController::class, 'contents'])->name('contents');
        Route::post('/konten', [AdminController::class, 'saveContent'])->name('contents.save');
        Route::delete('/konten/{content}', [AdminController::class, 'deleteContent'])->name('contents.delete');

        // DATA AUDITORIUM
        Route::get('/auditorium', [AdminController::class, 'auditoriums'])->name('auditoriums');
        Route::post('/auditorium', [AdminController::class, 'saveAuditorium'])->name('auditoriums.save');
        Route::put('/auditorium/{auditorium}', [AdminController::class, 'saveAuditorium'])->name('auditoriums.update');
        Route::patch('/auditorium/{auditorium}/status', [AdminController::class, 'toggleAuditoriumStatus'])->name('auditoriums.toggle');
        Route::delete('/auditorium/{auditorium}', [AdminController::class, 'deleteAuditorium'])->name('auditoriums.delete');

        // DATA PENGGUNA
        Route::get('/pengguna', [AdminController::class, 'users'])->name('users');

        // PROFIL ADMIN
        Route::get('/profil', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profil', [DashboardController::class, 'updateProfile'])->name('profile.update');

        // UBAH PASSWORD ADMIN
        Route::put('/profil/password', [DashboardController::class, 'updatePassword'])
            ->name('profile.password');
    });