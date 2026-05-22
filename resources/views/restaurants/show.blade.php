<div class="rating-distribution mt-4">
    <h4>Phân phối đánh giá</h4>
    @php $totalReviews = $restaurant->reviews->count() ?: 1; // Tránh lỗi chia cho 0 @endphp

    @foreach([5, 4, 3, 2, 1] as $star)
    @php
    $count = $ratingDistribution[$star];
    $percent = ($count / $totalReviews) * 100;
    @endphp
    <div class="d-flex align-items-center mb-2">
        <span class="me-2" style="width: 40px">{{ $star }} ⭐</span>
        <div class="progress flex-grow-1" style="height: 10px;">
            <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;"> </div>
            <span class="ms-2 text-muted" style="width: 30px">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    @if($canReview)
    <div class="alert alert-success mt-3">
        Bạn đã trải nghiệm nhà hàng này. <a href="#review-form">Viết đánh giá ngay!</a>
    </div>
    @else
    <div class="alert alert-secondary mt-3">
        Chỉ những khách hàng đã đặt bàn và hoàn tất bữa ăn mới có thể đánh giá.
    </div>
    @endif@extends('layouts.app')

    @section('content')
    <div class="container py-4">
        <div class="row">
            <!-- CỘT TRÁI: NỘI DUNG CHÍNH -->
            <div class="col-md-8">
                <!-- Ảnh banner -->
                <img src="{{ $restaurant->image_url ?? 'https://via.placeholder.com/1200x360' }}"
                    alt="{{ $restaurant->name }}" class="rounded mb-4 shadow-sm"
                    style="width: 100%; max-height: 360px; object-fit: cover;">

                <!-- Header Thông tin cơ bản -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="fw-bold mb-2">{{ $restaurant->name }}</h1>
                            <p class="text-muted mb-1">
                                <strong>Địa chỉ:</strong> {{ $restaurant->address }}
                            </p>
                            <p class="text-muted mb-1">
                                <strong>SĐT:</strong> {{ $restaurant->phone }}
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary fs-6">{{ $restaurant->category->name ?? 'Nhà hàng' }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-3">
                        <span class="badge bg-light text-dark border">
                            🕒 {{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}
                        </span>
                        <span class="badge bg-light text-dark border">
                            💰 {{ number_format($restaurant->price_min) }}đ -
                            {{ number_format($restaurant->price_max) }}đ
                        </span>
                    </div>
                </div>

                <!-- Đánh giá tổng quan -->
                <div class="card mb-4 shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center border-end">
                                @php
                                $totalReviews = array_sum($ratingDistribution);
                                $averageRating = $totalReviews > 0 ? $restaurant->reviews->avg('rating') : 0;
                                @endphp
                                <h2 class="display-4 fw-bold text-warning mb-0">{{ number_format($averageRating, 1) }}
                                </h2>
                                <div class="text-warning fs-5">
                                    @for($i = 1; $i <= 5; $i++) {{ $i <= round($averageRating) ? '★' : '☆' }} @endfor
                                        </div>
                                        <p class="text-muted mb-0">{{ $totalReviews }} đánh giá</p>
                                </div>
                                <div class="col-md-8">
                                    @for($i = 5; $i >= 1; $i--)
                                    @php
                                    $count = $ratingDistribution[$i] ?? 0;
                                    $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center mb-1">
                                        <div style="width: 30px;">{{ $i }} ★</div>
                                        <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="text-muted" style="width: 40px; text-align: right;">
                                            {{ $percent > 0 ? round($percent).'%' : '0%' }}</div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="restaurantTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="menu-tab" data-bs-toggle="tab"
                                data-bs-target="#menu" type="button" role="tab">Thực đơn</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="reviews-tab" data-bs-toggle="tab"
                                data-bs-target="#reviews" type="button" role="tab">Đánh giá</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info"
                                type="button" role="tab">Thông tin</button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content py-4" id="restaurantTabContent">

                        <!-- Tab Thực đơn -->
                        <div class="tab-pane fade show active" id="menu" role="tabpanel">
                            @php
                            $groupedMenuItems = $restaurant->menuItems->groupBy('type');
                            @endphp
                            @forelse($groupedMenuItems as $type => $items)
                            <h4 class="mt-3 mb-3 border-bottom pb-2">{{ $type }}</h4>
                            <div class="row">
                                @foreach($items as $item)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center border rounded p-2 h-100">
                                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/80' }}"
                                            alt="{{ $item->name }}" class="rounded me-3"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $item->name }}</h6>
                                            <p class="text-danger fw-bold mb-0">{{ number_format($item->price) }}đ</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @empty
                            <p class="text-muted">Nhà hàng chưa cập nhật thực đơn.</p>
                            @endforelse
                        </div>

                        <!-- Tab Đánh giá -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <!-- Form viết đánh giá -->
                            @if($canReview)
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-primary text-white fw-bold">Viết đánh giá của bạn</div>
                                <div class="card-body">
                                    <form action="{{ url('/reviews') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Chất lượng nhà hàng:</label>
                                            <div class="d-flex gap-3">
                                                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating"
                                                        id="star{{ $i }}" value="{{ $i }}" required>
                                                    <label class="form-check-label text-warning" for="star{{ $i }}">
                                                        {{ $i }} ★
                                                    </label>
                                            </div>
                                            @endfor
                                        </div>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label fw-bold">Chia sẻ trải nghiệm của bạn</label>
                                    <textarea class="form-control" id="content" name="content" rows="3"
                                        placeholder="Nhà hàng có ngon không? Phục vụ thế nào?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                                </form>
                            </div>
                        </div>
                        @endif

                        <!-- Danh sách đánh giá -->
                        <div class="review-list">
                            @forelse($restaurant->reviews as $review)
                            <div class="d-flex mb-4 border-bottom pb-3">
                                <!-- Avatar chữ cái đầu -->
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                    style="width: 48px; height: 48px; font-size: 1.2rem; font-weight: bold;">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 me-2">{{ $review->user->name }}</h6>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="text-warning mb-2" style="font-size: 0.9rem;">
                                        @for($i = 1; $i <= 5; $i++) {{ $i <= $review->rating ? '★' : '☆' }} @endfor
                                            </div>
                                            <p class="mb-0">{{ $review->content }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted">Chưa có đánh giá nào cho nhà hàng này.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab Thông tin -->
                        <div class="tab-pane fade" id="info" role="tabpanel">
                            <h5 class="fw-bold">Thông tin liên hệ</h5>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item px-0"><strong>📍 Địa chỉ:</strong> {{ $restaurant->address }}
                                </li>
                                <li class="list-group-item px-0"><strong>📞 Số điện thoại:</strong>
                                    {{ $restaurant->phone }}</li>
                                <li class="list-group-item px-0"><strong>🕒 Giờ hoạt động:</strong>
                                    {{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}</li>
                            </ul>

                            <h5 class="fw-bold">Mô tả</h5>
                            <p>{{ $restaurant->description ?? 'Đang cập nhật mô tả.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: SIDEBAR ĐẶT BÀN -->
                <div class="col-md-4">
                    <div class="card shadow-sm sticky-top" style="top: 80px; z-index: 1020;">
                        <div class="card-header bg-danger text-white text-center py-3">
                            <h5 class="mb-0 fw-bold">ĐẶT BÀN NGAY</h5>
                        </div>
                        <div class="card-body">
                            @auth
                            <form action="{{ url('/bookings/create') }}" method="GET">
                                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ngày đến</label>
                                    <input type="date" name="date" class="form-control" required
                                        min="{{ date('Y-m-d') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Giờ đến</label>
                                    <select name="time" class="form-select" required>
                                        <option value="">-- Chọn giờ --</option>
                                        @for($h = 7; $h <= 21; $h++) <option value="{{ sprintf('%02d:00', $h) }}">
                                            {{ sprintf('%02d:00', $h) }}</option>
                                            <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}
                                            </option>
                                            @endfor
                                            <option value="22:00">22:00</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Số khách</label>
                                    <input type="number" name="guests" class="form-control" min="1" value="2" required>
                                </div>

                                <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                                    XEM BÀN TRỐNG
                                </button>
                            </form>
                            @else
                            <div class="text-center py-4">
                                <p class="mb-3">Bạn cần đăng nhập để tiến hành đặt bàn tại nhà hàng này.</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 fw-bold">ĐĂNG
                                    NHẬP</a>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div> <!-- End Row 1 -->

            <!-- DƯỚI CÙNG: NHÀ HÀNG TƯƠNG TỰ -->
            @if($similar->count() > 0)
            <hr class="my-5">
            <h3 class="fw-bold mb-4">Nhà hàng tương tự</h3>
            <div class="row">
                @foreach($similar as $simRest)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm text-decoration-none border-0">
                        <a href="{{ route('restaurants.show', $simRest->id) }}" class="text-dark text-decoration-none">
                            <img src="{{ $simRest->image_url ?? 'https://via.placeholder.com/300x200' }}"
                                class="card-img-top" alt="{{ $simRest->name }}"
                                style="height: 160px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title fw-bold text-truncate" title="{{ $simRest->name }}">
                                    {{ $simRest->name }}</h6>
                                <p class="card-text text-muted small mb-1">
                                    <span class="text-warning">★</span> {{ number_format($simRest->rating ?? 5, 1) }}
                                </p>
                                <p class="card-text text-muted small text-truncate">
                                    📍 {{ $simRest->address }}
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endsection