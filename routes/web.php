<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// 1. KHAI BÁO CONTROLLERS
// ==========================================
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController; // Đã thêm để tối ưu code bên dưới

// Khai báo Controllers của Admin (Dùng alias để tránh trùng tên với Frontend)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

// ==========================================
// 2. ROUTES XÁC THỰC (AUTHENTICATION)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// ==========================================
// 3. ROUTES FRONTEND (DÀNH CHO KHÁCH HÀNG)
// ==========================================
// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Nhà hàng (Lưu ý: route search phải đặt trước route show {id} để tránh bị nhận diện nhầm)
Route::get('/restaurants/search', [RestaurantController::class, 'search'])->name('restaurants.search');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/my-bookings', [BookingController::class, 'history'])->name('bookings.history');

// Chức năng yêu cầu đăng nhập (Đặt bàn, Đánh giá, Profile)
Route::middleware('auth')->group(function () {

    // Đặt bàn
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Đánh giá
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Quản lý tài khoản (Profile)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
});

// ==========================================
// 4. ROUTES BACKEND (DÀNH CHO QUẢN TRỊ VIÊN)
// ==========================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Quản lý Danh mục
        Route::resource('categories', CategoryController::class);
        
        // Quản lý Nhà hàng
        Route::resource('restaurants', AdminRestaurantController::class);

        // Quản lý Bàn
        Route::put('tables/{id}/toggle-status', [TableController::class, 'toggleStatus'])->name('tables.toggleStatus');
        Route::resource('tables', TableController::class);

        // Quản lý Thực đơn (Menu)
        Route::put('menus/{id}/toggle', [MenuController::class, 'toggle'])->name('menus.toggle');
        Route::resource('menus', MenuController::class);

        // Quản lý Đơn đặt bàn
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::put('bookings/{id}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::put('bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::put('bookings/{id}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');

        // Quản lý Người dùng
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::put('users/{id}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggleBan');

        // Quản lý Đánh giá
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::delete('reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });

// ==========================================
// 5. TEST ROUTES (DỮ LIỆU MẪU)
// ==========================================
Route::get('/pump-reviews', function () {
    // Tìm quán Bún chả (Thay ID = 1 bằng ID thật của quán Bún Chả trong DB của bạn)
    $restaurant = \App\Models\Restaurant::find(1); 
    $user = \App\Models\User::first();

    if ($restaurant && $user) {
        $comments = [
            'Quán rộng rãi, sạch sẽ. Bún chả nướng rất thơm, nước chấm vừa miệng!',
            'Đỉnh cao ẩm thực Hà Nội. Mình ăn ở đây từ hồi sinh viên đến giờ hương vị vẫn không đổi.',
            'Nhân viên phục vụ nhiệt tình dù quán rất đông. Sẽ quay lại ủng hộ thường xuyên.'
        ];

        foreach ($comments as $comment) {
            \App\Models\Review::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'rating' => 5,
                'comment' => $comment,
            ]);
        }
        return 'Đã bơm đánh giá thành công!';
    }
    return 'Lỗi: Không tìm thấy nhà hàng hoặc user';
});