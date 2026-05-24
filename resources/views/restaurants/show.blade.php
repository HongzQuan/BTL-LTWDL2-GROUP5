@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">

    <!-- SECTION 1: GALLERY LƯỚI ẢNH -->
    <div class="row g-2 mb-4">
        <!-- Cột trái: Ảnh chính to -->
        <div class="col-md-8">
            <div class="position-relative h-100">
                <img src="{{ $restaurant->image ? asset('storage/' . $restaurant->image) : 'https://placehold.co/800x500?text=Anh+Chinh' }}"
                    class="w-100 h-100 object-fit-cover rounded-start shadow-sm"
                    style="max-height: 420px; min-height: 420px;"
                    alt="{{ $restaurant->name }}">
            </div>
        </div>
        <!-- Cột phải: Lưới 4 ảnh nhỏ -->
        <div class="col-md-4 d-none d-md-block">
            <div class="row g-2 h-100">
                <div class="col-6"><img src="https://placehold.co/400x300?text=Khong+gian" class="w-100 h-100 object-fit-cover shadow-sm" style="max-height: 206px;" alt="Không gian"></div>
                <div class="col-6"><img src="https://placehold.co/400x300?text=Mon+an+1" class="w-100 h-100 object-fit-cover rounded-end shadow-sm" style="max-height: 206px;" alt="Món ăn"></div>
                <div class="col-6"><img src="https://placehold.co/400x300?text=Mon+an+2" class="w-100 h-100 object-fit-cover shadow-sm" style="max-height: 206px;" alt="Món ăn"></div>
                <div class="col-6 position-relative">
                    <img src="https://placehold.co/400x300?text=Xem+them" class="w-100 h-100 object-fit-cover rounded-end shadow-sm" style="max-height: 206px;" alt="Xem thêm">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center rounded-end" style="cursor: pointer;">
                        <span class="text-white fw-bold"><i class="bi bi-images me-1"></i> Xem tất cả</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: BỐ CỤC 2 CỘT (Thông tin + Đặt bàn) -->
    <div class="row g-4 mb-5">

        <!-- CỘT TRÁI: THÔNG TIN CHI TIẾT -->
        <div class="col-lg-8">
            <!-- Box thông tin cơ bản -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h1 class="fw-bold mb-3 fs-2">{{ $restaurant->name }}</h1>
                    <div class="d-flex flex-column gap-2 text-muted">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt text-danger me-2 mt-1"></i>
                            <span>{{ $restaurant->address }}, {{ $restaurant->city }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            <span>SĐT: <strong class="text-dark">{{ $restaurant->phone ?: 'Đang cập nhật' }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-currency-dollar text-success me-2"></i>
                            <span>Khoảng giá: <strong class="text-dark">
                                    {{ $restaurant->price_min > 0 ? number_format($restaurant->price_min) . 'đ - ' . number_format($restaurant->price_max) . 'đ' : 'Đang cập nhật' }}
                                </strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-warning me-2"></i>
                            <span>Giờ mở cửa: <span class="text-success fw-bold">{{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Rating Tổng -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold text-warning mb-0 me-3">{{ number_format($averageRating ?? 0, 1) }}</h3>
                        <div class="text-warning fs-5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= round($averageRating ?? 0) ? '-fill' : '' }}"></i>
                                @endfor
                        </div>
                        <span class="text-muted ms-3 border-start ps-3">{{ $restaurant->reviews->count() ?? 0 }} lượt đánh giá</span>
                    </div>
                </div>
            </div>

            <!-- TABS THEO ĐÚNG YÊU CẦU: Thực đơn | Đánh giá | Thông tin -->
            <ul class="nav nav-tabs mb-4 border-bottom-0" id="restaurantTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-danger border-danger border-bottom-2 px-4" data-bs-toggle="tab" data-bs-target="#menu">Thực đơn</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark px-4 border-0" data-bs-toggle="tab" data-bs-target="#reviews">Đánh giá</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-dark px-4 border-0" data-bs-toggle="tab" data-bs-target="#info">Thông tin</button>
                </li>
            </ul>

            <div class="tab-content bg-white p-4 shadow-sm rounded border border-top-0 min-vh-50">

                <!-- TAB 1: THỰC ĐƠN (Nhóm theo loại) -->
                <div class="tab-pane fade show active" id="menu">
                    @if(isset($restaurant->menuItems) && $restaurant->menuItems->count() > 0)
                    @php
                    $groupedMenu = $restaurant->menuItems->groupBy('type');

                    // Từ điển dịch mã Database sang Tiếng Việt hiển thị cho đẹp
                    $typeNames = [
                    'mon_chinh' => 'Món Chính',
                    'khai_vi' => 'Món Khai Vị',
                    'trang_mieng' => 'Tráng Miệng',
                    'do_uong' => 'Đồ Uống',
                    'combo' => 'Set / Combo'
                    ];
                    @endphp

                    @foreach($groupedMenu as $type => $items)
                    @php
                    // Lấy tên Tiếng Việt, nếu mã nào chưa có trong từ điển thì viết hoa chữ cái đầu
                    $displayName = $typeNames[$type] ?? ucfirst(str_replace('_', ' ', $type));
                    @endphp

                    <div class="mb-4">
                        <h5 class="fw-bold text-danger text-uppercase mb-3 pb-2 border-bottom border-danger d-inline-block">{{ $displayName }}</h5>
                        <div class="row g-4 mt-0">
                            @foreach($items as $item)
                            <div class="col-lg-6">
                                <div class="d-flex align-items-end mb-1">
                                    <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                    <!-- Đường chấm (dotted) tạo phong cách menu nhà hàng -->
                                    <div class="flex-grow-1 mx-2" style="border-bottom: 2px dotted #dee2e6; position: relative; top: -6px;"></div>
                                    <div class="fw-bold text-danger fs-6">{{ number_format($item->price) }}đ</div>
                                </div>
                                @if($item->description)
                                <div class="text-muted small fst-italic w-75">{{ $item->description }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x display-4 mb-3 d-block"></i>
                        <p>Nhà hàng chưa cập nhật thực đơn chi tiết.</p>
                    </div>
                    @endif
                </div>

                <!-- TAB 2: ĐÁNH GIÁ (List review + Form viết bài) -->
                <div class="tab-pane fade" id="reviews">
                    <!-- Form viết đánh giá (Chỉ hiện khi đủ điều kiện đã đặt bàn) -->
                    @if(isset($canReview) && $canReview)
                    <div class="bg-light p-4 rounded mb-5 border border-info border-opacity-25">
                        <h6 class="fw-bold mb-3 text-info"><i class="bi bi-pencil-square me-1"></i> Chia sẻ trải nghiệm của bạn</h6>
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Đánh giá sao</label>
                                <select name="rating" class="form-select w-auto" required>
                                    <option value="5">⭐⭐⭐⭐⭐ Tuyệt vời</option>
                                    <option value="4">⭐⭐⭐⭐ Rất tốt</option>
                                    <option value="3">⭐⭐⭐ Bình thường</option>
                                    <option value="2">⭐⭐ Kém</option>
                                    <option value="1">⭐ Tệ</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="3" required placeholder="Món ăn, không gian, phục vụ..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">Gửi đánh giá</button>
                        </form>
                    </div>
                    @endif

                    <!-- Danh sách Review -->
                    <h5 class="fw-bold mb-4">Đánh giá từ thực khách ({{ $restaurant->reviews->count() }})</h5>
                    <div class="review-list">
                        @forelse($restaurant->reviews as $review)
                        <div class="d-flex mb-4 pb-4 border-bottom">
                            <!-- Avatar Random theo tên -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'User') }}&background=random" class="rounded-circle me-3 shadow-sm" style="width: 50px; height: 50px;" alt="Avatar">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $review->user->name ?? 'Thực khách ẩn danh' }}</h6>
                                <div class="text-warning small mb-2 d-flex align-items-center">
                                    <span class="me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                    </span>
                                    <span class="text-muted ms-2" style="font-size: 0.8rem;"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="mb-0 text-dark">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted fst-italic">Chưa có đánh giá nào cho nhà hàng này.</p>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 3: THÔNG TIN CHUNG -->
                <div class="tab-pane fade" id="info">
                    <h5 class="fw-bold mb-3">Giới thiệu chung</h5>
                    <p class="text-muted lh-lg">{{ $restaurant->description ?: 'Đang cập nhật thông tin giới thiệu.' }}</p>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: WIDGET ĐẶT CHỖ -->
        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-1">Đặt chỗ</h5>
                        <small class="text-danger fw-semibold">(Để có chỗ trước khi đến)</small>
                    </div>

                    <form action="{{ route('bookings.create') }}" method="GET">
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1"><i class="bi bi-person me-1"></i> Số khách (Người lớn & Trẻ em)</label>
                                <select name="guests" class="form-select text-dark" required>
                                    @for($i = 1; $i <= 30; $i++)
                                        <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} khách</option>
                                        @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small mb-1"><i class="bi bi-calendar3 me-1"></i> Thời gian đến</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <input type="time" name="time" class="form-control" value="18:30" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2 fs-6">ĐẶT CHỖ NGAY</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- SECTION 3: NHÀ HÀNG TƯƠNG TỰ (4 Card) -->
    <div class="mt-5 pt-4 border-top">
        <h4 class="fw-bold mb-4">Gợi ý nhà hàng tương tự</h4>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @forelse($similarRestaurants ?? [] as $similar)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;">
                        <img src="{{ $similar->image ? asset('storage/' . $similar->image) : 'https://placehold.co/400x300?text=No+Image' }}" class="w-100 h-100 object-fit-cover" alt="{{ $similar->name }}">
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75">{{ $similar->category->name ?? 'Nhà hàng' }}</span>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1 text-truncate"><a href="{{ route('restaurants.show', $similar->id) }}" class="text-dark text-decoration-none">{{ $similar->name }}</a></h6>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $similar->city }}</p>
                        <div class="text-warning small fw-bold">★ 5.0</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted">Không tìm thấy nhà hàng tương tự.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

<style>
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #dc3545 !important;
        background-color: transparent !important;
    }

    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection