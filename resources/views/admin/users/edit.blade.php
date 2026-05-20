@extends('layouts.admin')

@section('content')
<div class="card shadow-sm col-md-6 mx-auto">
    <div class="card-header bg-white fw-bold">Phân quyền Người dùng: {{ $user->name }}</div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Vai trò (Role)</label>
                <select name="role" class="form-select">
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Khách hàng (User)</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Trạng thái tài khoản</label>
                <select name="is_banned" class="form-select">
                    <option value="0" {{ $user->is_banned == 0 ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="1" {{ $user->is_banned == 1 ? 'selected' : '' }}>Khóa tài khoản</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection