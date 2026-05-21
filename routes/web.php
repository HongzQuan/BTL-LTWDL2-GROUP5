<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;

// ==========================================
// ROUTES CHO XÁC THỰC (ĐĂNG NHẬP / ĐĂNG KÝ)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ROUTES CHO QUẢN TRỊ VIÊN (ADMIN)
// ==========================================
// Áp dụng middleware 'auth' (bắt buộc đăng nhập) và 'admin' (bắt buộc role admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    // Sau này bạn sẽ thêm các route quản lý nhà hàng, user, booking... ở trong này
});


use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;

// Thay thế đoạn Route::get('/', function () { ... }) cũ bằng dòng này:
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {

    Route::get('/bookings',          [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create',   [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings',         [BookingController::class, 'store'])->name('bookings.store');

    // Các route cần xác minh chủ sở hữu
    Route::middleware(['booking.owner'])->group(function () {
        Route::get('/bookings/{id}',        [BookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });
});



