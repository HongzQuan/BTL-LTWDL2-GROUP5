@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('restaurants.index') }}" method="GET" class="row g-3 align-items-end">
                @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold small text-muted">Thành phố</label>
                    <select name="city" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold small text-muted">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold small text-muted">Giá tối thiểu</label>
                    <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}"
                        placeholder="VD: 100000">
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold small text-muted">Giá tối đa</label>
                    <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}"
                        placeholder="VD: 2000000">
                </div>

                <div class="col-md-3 col-sm-8">
                    <label class="form-label fw-semibold small text-muted">Từ khóa</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Tên hoặc địa chỉ...">
                </div>

                <div class="col-md-1 col-sm-4">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <div>
            <p class="text-muted mb-0">Tìm thấy <strong class="text-dark">{{ $restaurants->total() }}</strong> nhà hàng
                phù hợp</p>
        </div>

        <div class="btn-group shadow-sm" role="group">
            @php $currentParams = request()->except('sort'); @endphp
            <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'rating'])) }}"
                class="btn btn-outline-secondary btn-sm {{ request('sort') == 'rating' ? 'active' : '' }}">Nổi bật</a>
            <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'newest'])) }}"
                class="btn btn-outline-secondary btn-sm {{ (!request('sort') || request('sort') == 'newest') ? 'active' : '' }}">Mới
                nhất</a>
            <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'price_asc'])) }}"
                class="btn btn-outline-secondary btn-sm {{ request('sort') == 'price_asc' ? 'active' : '' }}">Giá ↑</a>
            <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'price_desc'])) }}"
                class="btn btn-outline-secondary btn-sm {{ request('sort') == 'price_desc' ? 'active' : '' }}">Giá ↓</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @forelse($restaurants as $restaurant)
        <div class="col">
            <div class="card restaurant-card h-100 border-0 shadow-sm hover-shadow transition-all position-relative">

                <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;">
                    <img src="{{ $restaurant->image ? '/' . $restaurant->image : 'https://placehold.co/600x450?text=No+Image' }}"
                        class="w-100 h-100 object-fit-cover" alt="{{ $restaurant->name }}">
                    <span
                        class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 backdrop-blur py-2 px-3 fs-7">{{ $restaurant->category->name }}</span>
                </div>

                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold mb-2">
                        <a href="{{ url('/restaurants/' . $restaurant->id) }}"
                            class="text-decoration-none text-dark link-primary">{{ $restaurant->name }}</a>
                    </h5>

                    <p class="card-text text-muted small mb-2 text-truncate">📍 {{ $restaurant->address }},
                        {{ $restaurant->district }}, {{ $restaurant->city }}
                    </p>

                    <div class="fw-bold text-danger mb-3 mt-1">
                        <i class="fas fa-tag me-1"></i>
                        {{ number_format($restaurant->price_min, 0, ',', '.') }}đ - {{ number_format($restaurant->price_max, 0, ',', '.') }}đ
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                        <div>
                            <span class="text-warning fw-bold">★</span>
                            <span class="fw-bold text-dark">{{ round($restaurant->average_rating ?? 0, 1) }}</span>
                        </div>
                        <div class="small text-muted">🕒
                            {{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 p-3 pt-0 text-center mt-auto">
                    <a href="{{ route('restaurants.show', $restaurant->id) }}" class="btn btn-primary w-100 fw-bold">Xem chi tiết & Đặt bàn</a>
                </div>

            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="mb-3 fs-1 text-muted">🍽️</div>
            <h4 class="fw-bold text-secondary">Không tìm thấy kết quả phù hợp</h4>
            <p class="text-muted">Bạn hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm xem sao nhé.</p>
            <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-sm mt-2">Đặt lại bộ lọc</a>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $restaurants->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
<style>
    .restaurant-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #fff;
    }

    .restaurant-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12) !important;
    }

    .restaurant-card img {
        transition: transform 0.45s ease;
    }

    .restaurant-card:hover img {
        transform: scale(1.08);
    }
</style>

@endsection