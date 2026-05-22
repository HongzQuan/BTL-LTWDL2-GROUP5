@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Quản lý Nhà hàng</h3>
    <a href="{{ route('restaurants.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Thêm mới</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên nhà hàng</th>
                        <th>Danh mục</th>
                        <th>Khu vực</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($restaurants as $restaurant)
                    <tr>
                        <td>{{ $restaurant->id }}</td>
                        <td>
                            @if($restaurant->image)
                            <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" width="60" class="rounded shadow-sm">
                            @else
                            <img src="https://via.placeholder.com/60?text=No+Image" alt="No Image" width="60" class="rounded shadow-sm">
                            @endif
                        </td>
                        <td class="fw-bold">{{ $restaurant->name }}</td>
                        <td>{{ $restaurant->category->name ?? 'N/A' }}</td>
                        <td>{{ $restaurant->district }}, {{ $restaurant->city }}</td>
                        <td>
                            @if($restaurant->status)
                            <span class="badge bg-success">Đang hoạt động</span>
                            @else
                            <span class="badge bg-secondary">Tạm đóng</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('restaurants.destroy', $restaurant) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà hàng này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có nhà hàng nào trong hệ thống.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $restaurants->links('pagination::bootstrap-5') }}
</div>
@endsection