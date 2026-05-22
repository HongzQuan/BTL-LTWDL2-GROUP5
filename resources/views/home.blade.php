@extends('layouts.app')

@section('content')
<!-- Hero Banner -->
<div class="bg-primary text-white text-center py-5">
    <div class="container py-5">
        <h1 class="display-4 fw-bold">Tìm Kiếm & Đặt Bàn Nhanh Chóng</h1>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <form action="/restaurants/search" method="GET" class="d-flex">
                    <input class="form-control form-control-lg me-2" type="search" name="q" placeholder="Nhập tên nhà hàng, khu vực...">
                    <button class="btn btn-warning btn-lg px-4" type="submit">Tìm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- Section: Chọn thành phố -->
    <h3 class="fw-bold mb-4">Khám phá thành phố</h3>
    <div class="row mb-5">
        @foreach(['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hội An'] as $city)
        <div class="col-md-3 col-6 mb-3">
            <a href="/restaurants?city={{ $city }}" class="card text-decoration-none shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <h5>{{ $city }}</h5>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Section: Danh mục -->
    <h3 class="fw-bold mb-4">Khám phá theo danh mục</h3>
    <div class="row mb-5">
        @foreach($categories as $category)
        <div class="col-auto mb-2">
            <a href="{{ route('frontend.category', $category->slug) }}" class="btn btn-outline-primary rounded-pill px-4">{{ $category->name }}</a>
        </div>
        @endforeach
    </div>

    <!-- Section: Nhà hàng nổi bật (Top 8) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Nhà hàng nổi bật</h3>
        <a href="/restaurants" class="text-primary">Xem tất cả</a>
    </div>
    <div class="row mb-5">
        @foreach($topRestaurants as $restaurant)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ asset('storage/' . $restaurant->image) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                <div class="card-body">
                    <h6 class="card-title fw-bold">{{ $restaurant->name }}</h6>
                    <p class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $restaurant->city }}</p>
                    <a href="#" class="btn btn-sm btn-outline-primary w-100" @guest onclick="event.preventDefault(); window.location.href='/login';" @endguest>Đặt bàn ngay</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Section: Khu phố ẩm thực -->
    <h3 class="fw-bold mb-4">Khu phố ẩm thực</h3>
    <div class="row mb-5">
        @foreach(['Phố cổ', 'Bùi Viện', 'Ven biển', 'Trung tâm'] as $area)
        <div class="col-md-3 mb-3">
            <div class="card border-0 text-white">
                <img src="https://via.placeholder.com/300x200" class="card-img" alt="...">
                <div class="card-img-overlay d-flex align-items-end">
                    <h5 class="card-title fw-bold">{{ $area }} (10 nhà hàng)</h5>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Section: Nhà hàng mới -->
    <h3 class="fw-bold mb-4">Nhà hàng mới thêm</h3>
    <div class="row">
        @foreach($newRestaurants as $restaurant)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="row g-0">
                    <div class="col-4"><img src="{{ asset('storage/' . $restaurant->image) }}" class="img-fluid h-100" style="object-fit:cover"></div>
                    <div class="col-8">
                        <div class="card-body">
                            <h6 class="card-title">{{ $restaurant->name }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection