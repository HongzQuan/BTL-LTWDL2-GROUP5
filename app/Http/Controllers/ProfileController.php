<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // 1. Hiển thị giao diện Profile và Lịch sử đặt bàn
    public function index() {
        $user = auth()->user();
        
        // Lấy 5 lịch sử đặt bàn gần nhất của user này
        $bookings = $user->bookings()->latest()->take(5)->get();
        
        return view('profile', compact('user', 'bookings'));
    }

    // 2. Xử lý cập nhật thông tin cá nhân
    public function update(Request $request) {
        $user = auth()->user();
        
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Lưu dữ liệu mới
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // 3. Xử lý đổi mật khẩu
    public function changePassword(Request $request) {
        // Kiểm tra dữ liệu nhập vào (Mật khẩu mới phải >= 8 ký tự và nhập lại phải khớp)
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Kiểm tra xem mật khẩu cũ nhập vào có đúng không
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác']);
        }

        // Đổi sang mật khẩu mới đã được mã hóa (Hash)
        $user->update(['password' => Hash::make($request->new_password)]);
        
        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}
