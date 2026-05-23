@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Đánh giá</h1>
    </div>

    <!-- Thông báo Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Badge Thống kê Số sao -->
    <div class="mb-4">
        <span class="fw-bold me-2">Tổng quan đánh giá:</span>
        @for($i = 5; $i >= 1; $i--)
            <span class="badge bg-warning text-dark me-2 py-2 px-3 shadow-sm">
                {{ $i }} <i class="bi bi-star-fill"></i> ({{ $ratingStats[$i] ?? 0 }})
            </span>
        @endfor
    </div>

    <!-- Form Bộ lọc -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="restaurant_id" class="form-select">
                        <option value="">-- Tất cả Nhà hàng --</option>
                        @foreach($restaurants as $res)
                            <option value="{{ $res->id }}" {{ request('restaurant_id') == $res->id ? 'selected' : '' }}>
                                {{ $res->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="rating" class="form-select">
                        <option value="">-- Tất cả mức sao --</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Sao
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary w-100">Bỏ lọc</a>
                </div>
            </form>
        </div>

        <!-- Bảng Dữ liệu -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">Avatar</th>
                            <th width="15%">Người dùng</th>
                            <th width="15%">Nhà hàng</th>
                            <th width="12%">Đánh giá</th>
                            <th width="35%">Nội dung</th>
                            <th width="10%">Ngày viết</th>
                            <th width="8%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <!-- Avatar chữ cái đầu -->
                                <td class="text-center">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto fw-bold" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                </td>
                                
                                <td><span class="fw-bold">{{ $review->user->name ?? 'Khách Ẩn Danh' }}</span></td>
                                <td>{{ $review->restaurant->name ?? 'N/A' }}</td>
                                
                                <!-- Render Sao -->
                                <td>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                
                                <!-- Nội dung có Tooltip -->
                                <td>
                                    <span title="{{ $review->comment }}" style="cursor: help;">
                                        {{ \Illuminate\Support\Str::limit($review->comment, 80, '...') }}
                                    </span>
                                </td>
                                
                                <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                
                                <!-- Nút Xóa có JS Confirm -->
                                <td>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="bi bi-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không tìm thấy đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="mt-3">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection