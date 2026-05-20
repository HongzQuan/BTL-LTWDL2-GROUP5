<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // Không cho phép tự sửa quyền của chính mình để tránh mất quyền admin
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Không thể tự chỉnh sửa quyền của bản thân!');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Lỗi phân quyền!');
        }

        $request->validate([
            'role' => 'required|in:admin,user',
            'is_banned' => 'required|boolean',
        ]);

        $user->update([
            'role' => $request->role,
            'is_banned' => $request->is_banned,
        ]);

        return redirect()->route('users.index')->with('success', 'Đã cập nhật thông tin người dùng!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự xóa tài khoản của mình!');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Đã xóa người dùng!');
    }
}