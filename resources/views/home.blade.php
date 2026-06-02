@extends('layouts.app')

@section('content')
<!-- SECTION 1: HERO BANNER TÌM KIẾM -->
<div class="position-relative d-flex align-items-center justify-content-center" style="min-height: 480px; background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.65;"></div>

    <div class="position-relative text-center text-white w-100 px-3" style="max-width: 800px; z-index: 2;">
        <h1 class="display-4 fw-bold mb-3 shadow-sm">Khám Phá Ẩm Thực Việt Nam</h1>
        <p class="fs-5 mb-4">Tìm kiếm và đặt bàn nhanh chóng tại hàng ngàn nhà hàng uy tín</p>

        <form action="/restaurants/search" method="GET" class="mb-3">
            <div class="input-group input-group-lg bg-white rounded-pill p-1 shadow-lg">
                <span class="input-group-text bg-transparent border-0 text-muted ps-4"><i class="bi bi-search"></i></span>
                <input class="form-control border-0 shadow-none px-2" type="search" name="q" placeholder="Nhập tên nhà hàng, khu vực, món ăn...">
                <button class="btn btn-danger rounded-pill px-4 fw-bold text-uppercase" type="submit">Tìm kiếm</button>
            </div>
        </form>

        <div class="d-flex flex-wrap justify-content-center gap-2 small">
            <span class="text-white-50">Tìm kiếm phổ biến:</span>
            <a href="/restaurants/search?q=Lẩu" class="text-white text-decoration-none bg-light bg-opacity-25 px-2 rounded-pill hover-tag">Lẩu</a>
            <a href="/restaurants/search?q=Hải sản" class="text-white text-decoration-none bg-light bg-opacity-25 px-2 rounded-pill hover-tag">Hải sản</a>
            <a href="/restaurants/search?q=Buffet" class="text-white text-decoration-none bg-light bg-opacity-25 px-2 rounded-pill hover-tag">Buffet</a>
            <a href="/restaurants/search?q=Món Âu" class="text-white text-decoration-none bg-light bg-opacity-25 px-2 rounded-pill hover-tag">Món Âu</a>
        </div>
    </div>
</div>

<div class="container my-5">

    <!-- SECTION 2: DANH MỤC TRƯỢT NGANG (Đã căn giữa & Cập nhật Icon) -->
    <div class="mb-5 text-center">
        <h4 class="fw-bold mb-4 text-dark">Khám phá theo danh mục</h4>
        <div class="d-flex justify-content-center flex-wrap gap-4 pb-3">
            @foreach($categories as $category)
            @php
            $catName = mb_strtolower($category->name);
            $iconClass = 'bi-shop text-dark'; // Mặc định

            if (str_contains($catName, 'lẩu') || str_contains($catName, 'nướng')) $iconClass = 'bi-fire text-danger';
            elseif (str_contains($catName, 'hải sản')) $iconClass = 'bi-water text-info';
            elseif (str_contains($catName, 'cơm') || str_contains($catName, 'cháo') || str_contains($catName, 'phở') || str_contains($catName, 'bún')) $iconClass = 'bi-cup-hot text-warning';
            elseif (str_contains($catName, 'chay') || str_contains($catName, 'healthy')) $iconClass = 'bi-flower1 text-success';
            elseif (str_contains($catName, 'nhậu') || str_contains($catName, 'bia')) $iconClass = 'bi-cup-straw text-warning';
            elseif (str_contains($catName, 'buffet')) $iconClass = 'bi-collection text-primary';
            elseif (str_contains($catName, 'âu') || str_contains($catName, 'á')) $iconClass = 'bi-globe-americas text-secondary';
            @endphp
            <a href="{{ route('restaurants.index', ['category_id' => $category->id]) }}" class="text-decoration-none text-dark text-center category-item category-hover transition-all flex-shrink-0" style="width: 85px;">
                <div class="rounded-circle bg-white shadow-sm border d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 70px; height: 70px;">
                    <i class="bi {{ $iconClass }} fs-2"></i>
                </div>
                <span class="small fw-semibold text-truncate d-block">{{ $category->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- SECTION 3: TOP NHÀ HÀNG NỔI BẬT -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h4 class="fw-bold m-0 text-dark">Nhà hàng nổi bật</h4>
        <a href="/restaurants" class="text-danger text-decoration-none fw-bold small">Xem tất cả <i class="bi bi-chevron-right"></i></a>
    </div>

    <div class="row g-4 mb-5">
        @foreach($topRestaurants as $restaurant)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 border rounded-4 overflow-hidden shadow-sm hover-card transition-all">
                <!-- Ảnh Nhà hàng và Nhãn dán -->
                <div class="position-relative w-100" style="height: 200px;">
                    <!-- ĐÃ TRẢ LẠI CHỮ HOT Ở ĐÂY -->
                    <span class="position-absolute top-0 start-0 bg-danger text-white px-3 py-1 fw-bold small shadow-sm" style="border-bottom-right-radius: 12px; z-index: 2; font-size: 0.75rem;">
                        HOT 🔥
                    </span>

                    <img src="{{ $restaurant->image ? asset($restaurant->image) : 'https://placehold.co/400x300?text=No+Image' }}"
                        class="w-100 h-100 object-fit-cover" alt="{{ $restaurant->name }}">
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column bg-white">
                    <!-- Tên nhà hàng -->
                    <h5 class="card-title fw-bold text-truncate mb-2 text-dark" title="{{ $restaurant->name }}">
                        {{ $restaurant->name }}
                    </h5>

                    <!-- Địa chỉ -->
                    <div class="mb-2">
                        <span class="text-muted small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $restaurant->city }}</span>
                    </div>

                    <!-- Số sao đánh giá -->
                    <div class="mb-3 small">
                        <span class="text-warning fw-bold">
                            <i class="bi bi-star-fill"></i>
                            {{ $restaurant->average_rating ? round($restaurant->average_rating, 1) : '5.0' }}
                        </span>
                    </div>

                    <!-- Giá tiền (Lấy Data thật) -->
                    <p class="small text-dark fw-semibold mb-4 border-top pt-3 mt-auto">
                        @if($restaurant->price_min > 0)
                        <span class="text-danger fs-6">{{ number_format($restaurant->price_min) }}đ - {{ number_format($restaurant->price_max) }}đ</span>
                        @else
                        <span class="text-muted fst-italic">Giá: Đang cập nhật</span>
                        @endif
                    </p>

                    <!-- Nút Đặt chỗ -->
                    <a href="{{ route('restaurants.show', $restaurant->id) }}" class="btn btn-outline-danger w-100 fw-bold py-2 rounded-pill btn-book transition-all">
                        Đặt chỗ ngay
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- BANNER QUẢNG CÁO -->
    <div class="rounded-4 overflow-hidden mb-5 shadow-sm position-relative banner-promo">
        <img src="https://images.unsplash.com/photo-1543362906-acfc16c67564?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" class="w-100 object-fit-cover" style="height: 300px;" alt="Banner">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.4;"></div>
        <div class="position-absolute top-50 translate-middle-y ms-5 text-white d-none d-md-block" style="z-index: 2;">
            <h2 class="fw-bold mb-2">Trải Nghiệm Ẩm Thực Đỉnh Cao</h2>
            <p class="mb-4 fs-5">Hàng ngàn ưu đãi độc quyền chỉ có tại hệ thống của chúng tôi.</p>
            <a href="/restaurants" class="btn btn-danger fw-bold px-4 py-2 rounded-pill">Khám phá ngay</a>
        </div>
        <div class="position-absolute top-50 start-50 translate-middle text-white text-center w-100 px-3 d-md-none" style="z-index: 2;">
            <h3 class="fw-bold mb-2">Ẩm Thực Đỉnh Cao</h3>
            <a href="/restaurants" class="btn btn-sm btn-danger fw-bold px-3 rounded-pill">Khám phá ngay</a>
        </div>
    </div>

    <!-- SECTION 4: GỢI Ý THEO KHU VỰC (Đã thay link ảnh chuẩn, không bị trắng xóa) -->
    <h4 class="fw-bold mb-4 text-dark">Gợi ý theo khu vực</h4>
    <div class="row g-3 mb-5">
        @php
        $cityData = [
        'Hà Nội' => 'https://images.unsplash.com/photo-1528127269322-539801943592?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'TP.HCM' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'Đà Nẵng' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'Hội An' => 'https://statics.vinpearl.com/hoi-an-o-dau-thumb_1697556724.jpg'
        ];
        @endphp
        @foreach($cityData as $city => $imgUrl)
        <div class="col-md-3 col-6">
            <a href="/restaurants?city={{ $city }}" class="card text-decoration-none border-0 overflow-hidden rounded-4 hover-card transition-all text-white h-100 shadow-sm">
                <img src="{{ $imgUrl }}" class="card-img h-100 object-fit-cover" style="min-height: 250px;" alt="{{ $city }}">
                <div class="card-img-overlay d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 60%);">
                    <h5 class="fw-bold mb-1">{{ $city }}</h5>
                    <small class="text-light opacity-75">{{ \App\Models\Restaurant::where('city', 'LIKE', '%' . $city . '%')->count() }} nhà hàng</small>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- SECTION 5: NHÀ HÀNG MỚI -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h4 class="fw-bold m-0 text-dark">Nhà hàng mới ra mắt</h4>
    </div>

    <div class="row g-3">
        @foreach($newRestaurants as $restaurant)
        @if($loop->iteration > 6) @break @endif
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border rounded-3 hover-card transition-all text-decoration-none h-100">
                <div class="row g-0 h-100">
                    <div class="col-4 position-relative">
                        <!-- Badge MỚI ôm sát góc ảnh -->
                        <span class="position-absolute top-0 start-0 bg-success text-white px-2 py-1 fw-bold shadow-sm" style="border-bottom-right-radius: 8px; font-size: 0.7rem; z-index: 2;">MỚI</span>

                        <img src="{{ $restaurant->image ? asset($restaurant->image) : 'https://placehold.co/200x200?text=New' }}"
                            class="img-fluid h-100 w-100 object-fit-cover rounded-start" alt="{{ $restaurant->name }}">
                    </div>
                    <div class="col-8">
                        <div class="card-body py-2 px-3 d-flex flex-column justify-content-center h-100">
                            <h6 class="card-title fw-bold text-dark mb-1 text-truncate">{{ $restaurant->name }}</h6>
                            <small class="text-muted mb-1"><i class="bi bi-geo-alt text-danger"></i> {{ $restaurant->city }}</small>

                            <div class="small mb-1">
                                @if($restaurant->average_rating)
                                <span class="text-warning"><i class="bi bi-star-fill"></i> {{ round($restaurant->average_rating, 1) }}</span>
                                @else
                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">Chưa có đánh giá</span>
                                @endif
                            </div>

                            <div class="text-dark fw-semibold small mt-auto">
                                @if($restaurant->price_min > 0)
                                {{ number_format($restaurant->price_min) }}đ+
                                @else
                                Giá: Liên hệ
                                @endif
                            </div>
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-between align-items-end mb-3 mt-5 pt-3" id="collections">
        <div>
            <h4 class="fw-bold m-0 text-dark">Khám phá Bộ sưu tập</h4>
            <p class="text-muted small mt-1 mb-0">Những địa điểm ăn uống được tuyển chọn kỹ lưỡng</p>
        </div>
        <a href="#" class="text-danger text-decoration-none fw-bold small">Xem tất cả <i class="bi bi-chevron-right"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <a href="/restaurants?collection=nhau-xuyen-dem" class="card border-0 text-white overflow-hidden rounded-4 shadow-sm hover-card transition-all d-block text-decoration-none">
                <img src="https://aeonmall-review-rikkei.cdn.vccloud.vn/website/21/articles/May2025/TDcbHYT0akEFTBMfVKgE.jpg" class="card-img w-100 object-fit-cover" style="height: 220px;" alt="Quán nhậu">
                <div class="card-img-overlay d-flex flex-column justify-content-end p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.1) 100%);">
                    <h5 class="fw-bold mb-1">Top Quán Nhậu Lai Rai</h5>
                    <span class="badge bg-danger align-self-start">12 Nhà hàng</span>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="/restaurants?collection=lang-man" class="card border-0 text-white overflow-hidden rounded-4 shadow-sm hover-card transition-all d-block text-decoration-none">
                <img src="https://images.unsplash.com/photo-1514933651103-005eec06c04b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img w-100 object-fit-cover" style="height: 220px;" alt="Lãng mạn">
                <div class="card-img-overlay d-flex flex-column justify-content-end p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.1) 100%);">
                    <h5 class="fw-bold mb-1">Hẹn Hò Lãng Mạn</h5>
                    <span class="badge bg-danger align-self-start">8 Nhà hàng</span>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-12">
            <a href="/restaurants?collection=buffet-hai-san" class="card border-0 text-white overflow-hidden rounded-4 shadow-sm hover-card transition-all d-block text-decoration-none">
                <img src="https://images.unsplash.com/photo-1615719413546-198b25453f85?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img w-100 object-fit-cover" style="height: 220px;" alt="Hải sản">
                <div class="card-img-overlay d-flex flex-column justify-content-end p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.1) 100%);">
                    <h5 class="fw-bold mb-1">Buffet Hải Sản Cao Cấp</h5>
                    <span class="badge bg-danger align-self-start">15 Nhà hàng</span>
                </div>
            </a>
        </div>
    </div>
    <div class="container my-5 pt-5 border-top" id="blog">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Cẩm nang ẩm thực</h3>
                <p class="text-muted mb-0">Khám phá những mẹo hay và review nhà hàng chân thực nhất.</p>
            </div>
            <a href="#" class="text-danger text-decoration-none fw-semibold">Xem tất cả <i class="bi bi-chevron-right small"></i></a>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-card transition-all rounded-4 overflow-hidden bg-white">
                    <div class="position-relative w-100" style="height: 220px;">
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="w-100 h-100 object-fit-cover" alt="Blog 1">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-danger rounded-pill px-3 py-2">Review</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i> 26/05/2026</div>
                        <h5 class="fw-bold mb-3"><a href="#" class="text-dark text-decoration-none nav-hover-danger">Top 5 quán bún chả ngon nức tiếng tại Hà Nội bạn phải thử</a></h5>
                        <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">Bún chả Hà Nội từ lâu đã là món ăn quốc dân. Cùng khám phá những địa chỉ giữ được hương vị truyền thống chuẩn vị nhất...</p>
                        <a href="#" class="text-danger fw-bold text-decoration-none mt-auto">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-card transition-all rounded-4 overflow-hidden bg-white">
                    <div class="position-relative w-100" style="height: 220px;">
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="w-100 h-100 object-fit-cover" alt="Blog 2">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-success rounded-pill px-3 py-2">Mẹo hay</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i> 24/05/2026</div>
                        <h5 class="fw-bold mb-3"><a href="#" class="text-dark text-decoration-none nav-hover-danger">Bí quyết đặt bàn nhà hàng mùa cao điểm không sợ hết chỗ</a></h5>
                        <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">Các dịp lễ tết luôn là nỗi ám ảnh khi đi ăn hàng. Nắm ngay các mẹo sau để luôn có bàn đẹp cho gia đình và bạn bè...</p>
                        <a href="#" class="text-danger fw-bold text-decoration-none mt-auto">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-card transition-all rounded-4 overflow-hidden bg-white">
                    <div class="position-relative w-100" style="height: 220px;">
                        <img src="https://thotrang.vn/Uploads/2060/images/horeca-la-gi.jpg" class="w-100 h-100 object-fit-cover" alt="Blog 3">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark rounded-pill px-3 py-2">Khám phá</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i> 20/05/2026</div>
                        <h5 class="fw-bold mb-3"><a href="#" class="text-dark text-decoration-none nav-hover-danger">Xu hướng HORECA: Các nhà hàng đang chuyển đổi số như thế nào?</a></h5>
                        <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">Áp dụng công nghệ vào quy trình vận hành và marketing đang trở thành xu hướng tất yếu của ngành F&B tại Việt Nam...</p>
                        <a href="#" class="text-danger fw-bold text-decoration-none mt-auto">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }

    .transition-all {
        transition: all 0.25s ease-in-out;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .hover-tag:hover {
        background-color: rgba(255, 255, 255, 0.5) !important;
        color: #fff !important;
    }

    .category-hover:hover div {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        transform: translateY(-5px);
    }

    .category-hover:hover div i {
        color: white !important;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .form-control:focus {
        box-shadow: none;
        outline: none;
    }
</style>
@endsection