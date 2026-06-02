<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = 'login.' . $request->ip();

        // Kiểm tra Rate Limit (5 lần/phút)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau {$seconds} giây."
            ])->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Chuyển hướng theo role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            }
            return redirect()->intended('/');
        }

        RateLimiter::hit($throttleKey, 60); // Khóa 60 giây nếu sai

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric|digits:10',
            'password' => 'required|string|min:6|confirmed', // Cần field password_confirmation ở form
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user', // Mặc định là user
        ]);

        // Auto login sau khi đăng ký
        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        // Đăng xuất user hiện tại
        Auth::logout();

        // Hủy toàn bộ dữ liệu session cũ
        $request->session()->invalidate();

        // Tạo lại CSRF token mới để bảo mật, chống lỗi 419 Page Expired
        $request->session()->regenerateToken();

        // Chuyển hướng người dùng về trang chủ
        return redirect('/');
    }
}
