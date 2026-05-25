@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Quản lý Nhà hàng</h2>
            <p class="text-muted mb-0 mt-1">Danh sách tất cả các nhà hàng đang hoạt động </p>
        </div>
        <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary fw-bold shadow-sm px-4">
            + Thêm nhà hàng mới
        </a>
    </div>

    <!-- Thông báo Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <strong class="me-1">Thành công!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Card Bảng dữ liệu -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4 text-center text-secondary" style="width: 5%;">ID</th>
                            <th scope="col" class="text-secondary" style="width: 15%;">Hình ảnh</th>
                            <th scope="col" class="text-secondary" style="width: 25%;">Thông tin nhà hàng</th>
                            <th scope="col" class="text-secondary" style="width: 20%;">Địa điểm</th>
                            <th scope="col" class="text-secondary" style="width: 15%;">Trạng thái</th>
                            <th scope="col" class="text-end pe-4 text-secondary" style="width: 20%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($restaurants as $restaurant)
                        <tr>
                            <td class="ps-4 text-center fw-semibold text-muted">#{{ $restaurant->id }}</td>
                            <td>
                                <img src="{{ $restaurant->image ? asset('storage/' . $restaurant->image) : 'https://placehold.co/100x75?text=No+Image' }}"
                                    alt="{{ $restaurant->name }}" class="rounded shadow-sm object-fit-cover"
                                    style="width: 90px; height: 65px;">
                            </td>
                            <td>
                                <p class="fw-bold text-dark mb-1">{{ $restaurant->name }}</p>
                                <span
                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                    {{ $restaurant->category->name ?? 'Chưa phân loại' }}
                                </span>
                            </td>
                            <td>
                                <p class="mb-1 small fw-semibold">📍 {{ $restaurant->city }}</p>
                                @if($restaurant->district)
                                <p class="mb-0 text-muted small">{{ $restaurant->district }}</p>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    Đang hoạt động
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <!-- Nút Sửa -->
                                    <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                        Sửa
                                    </a>
                                    <!-- Nút Xóa (Dùng Form vì Route Destroy bắt buộc method DELETE) -->
                                    <form action="{{ route('admin.restaurants.destroy', $restaurant->id) }}"
                                        method="POST" class="d-inline-block m-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà hàng [{{ $restaurant->name }}] không? Hành động này không thể hoàn tác!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="fs-1 mb-3">🍽️</div>
                                <h5 class="fw-bold">Chưa có dữ liệu nhà hàng</h5>
                                <p>Hệ thống hiện tại chưa có nhà hàng nào. Hãy bấm "Thêm nhà hàng mới" để bắt đầu!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Card: Phân trang -->
        @if($restaurants->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $restaurants->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection