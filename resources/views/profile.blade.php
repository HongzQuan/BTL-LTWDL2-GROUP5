@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-badge text-danger me-2"></i>Thông tin cá nhân</h5>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Email (Không thể thay đổi)</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger px-4 mt-4 rounded-pill fw-bold">Lưu thay đổi</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock text-danger me-2"></i>Đổi mật khẩu</h5>
                    <form action="{{ route('profile.changePassword') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-danger px-4 mt-4 rounded-pill fw-bold">Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-clock-history text-danger me-2"></i>Đặt bàn gần đây</h5>
                        <a href="#" class="small text-danger text-decoration-none fw-bold">Xem tất cả</a>
                    </div>

                    @if($bookings->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                        <p class="text-muted small mt-2">Bạn chưa có lịch đặt bàn nào.</p>
                    </div>
                    @else
                    @foreach($bookings as $booking)
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <h6 class="fw-bold mb-1 text-truncate">{{ $booking->restaurant->name ?? 'Nhà hàng không xác định' }}</h6>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-calendar-event me-1"></i> {{ date('d/m/Y', strtotime($booking->booking_date)) }}
                            <i class="bi bi-clock ms-2 me-1"></i> {{ $booking->booking_time }}
                        </div>
                        <span class="badge 
    @if($booking->status == 'pending') bg-warning text-dark
    @elseif($booking->status == 'confirmed') bg-success
    @elseif($booking->status == 'cancelled') bg-danger
    @endif">

                            @if($booking->status == 'pending') Chờ xử lý
                            @elseif($booking->status == 'confirmed') Đã xác nhận
                            @elseif($booking->status == 'cancelled') Đã hủy
                            @endif
                        </span>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection