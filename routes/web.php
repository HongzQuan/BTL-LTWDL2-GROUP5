<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\RestaurantController;
use Illuminate\Support\Facades\File;
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
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::resource('categories', CategoryController::class);

        Route::resource('users', UserController::class);

        Route::resource('restaurants', RestaurantController::class);
        // ==========================
        // ADMIN BOOKING
        // ==========================

        Route::get(
            'bookings',
            [AdminBookingController::class, 'index']
        )->name(
            'admin.bookings.index'
        );

        Route::put(
            'bookings/{id}/confirm',
            [AdminBookingController::class, 'confirm']
        )->name(
            'admin.bookings.confirm'
        );

        Route::put(
            'bookings/{id}/cancel',
            [AdminBookingController::class, 'cancel']
        )->name(
            'admin.bookings.cancel'
        );

        Route::put(
            'bookings/{id}/complete',
            [AdminBookingController::class, 'complete']
        )->name(
            'admin.bookings.complete'
        );
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



Route::get('/category/{slug}', [App\Http\Controllers\HomeController::class, 'category'])->name('frontend.category');
Route::get('/category/{slug}', function ($slug) {
    return "Trang hiển thị các nhà hàng thuộc danh mục: " . $slug;
})->name('frontend.category');




Route::get('/storage/restaurants/{filename}', function ($filename) {
    $path = storage_path('app/public/restaurants/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    // Dùng helper response() có sẵn của Laravel để xuất file trực tiếp
    return response()->file($path);
});
