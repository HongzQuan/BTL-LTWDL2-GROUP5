@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Quản lý Khách hàng / Người dùng</h3>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="fw-bold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_banned)
                            <span class="badge bg-secondary">Bị khóa</span>
                        @else
                            <span class="badge bg-success">Hoạt động</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
@endsection