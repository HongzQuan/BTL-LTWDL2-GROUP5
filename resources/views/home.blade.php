@extends('layouts.app')

@section('content')
<div class="bg-primary text-white text-center py-5 mb-5">
    <div class="container py-5">
        <h1 class="display-4 fw-bold">Tìm Kiếm & Đặt Bàn Nhanh Chóng</h1>
        <p class="lead mb-4">Khám phá hàng trăm nhà hàng tuyệt vời và đặt bàn ngay hôm nay!</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="d-flex">
                    <input class="form-control form-control-lg me-2" type="search" placeholder="Nhập tên nhà hàng, khu vực...">
                    <button class="btn btn-warning btn-lg px-4" type="submit">Tìm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <h3 class="fw-bold mb-4">Khám phá theo danh mục</h3>
    <div class="row mb-5">
        @forelse($categories as $category)
            <div class="col-md-2 col-6 mb-3">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-tag fs-2 text-primary"></i>
                        <h6 class="card-title mt-2">{{ $category->name }}</h6>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">Chưa có danh mục nào.</p></div>
        @endforelse
    </div>

    <h3 class="fw-bold mb-4">Nhà hàng nổi bật</h3>
    <div class="row">
        @forelse($restaurants as $restaurant)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="{{ $restaurant->image ?? 'https://via.placeholder.com/400x250?text=No+Image' }}" class="card-img-top" alt="{{ $restaurant->name }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $restaurant->name }}</h5>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {{ $restaurant->district }}, {{ $restaurant->city }}</p>
                        <p class="card-text text-truncate">{{ $restaurant->description }}</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="text-danger fw-bold">{{ number_format($restaurant->price_range, 0, ',', '.') }} VNĐ</span>
                            <a href="#" class="btn btn-outline-primary btn-sm">Đặt bàn ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">Chưa có nhà hàng nào trên hệ thống.</p></div>
        @endforelse
    </div>
</div>
@endsection