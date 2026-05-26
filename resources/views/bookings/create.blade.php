@extends('layouts.app')

@section('title', 'Đặt bàn tại ' . $restaurant->name)

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4 mt-2">
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item">
                <a href="/" class="text-decoration-none text-muted transition-all link-hover-danger">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-decoration-none text-muted transition-all link-hover-danger">{{ $restaurant->name }}</a>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Đặt bàn</li>
        </ol>
    </nav>

    <style>
        .link-hover-danger:hover {
            color: #dc3545 !important;
            /* Chuyển sang màu đỏ PasGo khi rê chuột */
        }
    </style>

    <div class="row g-4">

        {{-- ===== CỘT TRÁI: FORM ===== --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Đặt bàn tại <strong>{{ $restaurant->name }}</strong>
                    </h4>
                </div>

                <div class="card-body p-4">

                    {{-- Thông tin đã chọn (readonly) --}}
                    <div class="alert alert-light border mb-4">
                        <h6 class="fw-semibold text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Thông tin tìm kiếm
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label small text-muted mb-1">Ngày đặt</label>
                                <input type="text"
                                    class="form-control form-control-sm bg-light"
                                    value="{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}"
                                    readonly>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small text-muted mb-1">Giờ đặt</label>
                                <input type="text"
                                    class="form-control form-control-sm bg-light"
                                    value="{{ $time }}"
                                    readonly>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small text-muted mb-1">Số khách</label>
                                <input type="text"
                                    class="form-control form-control-sm bg-light"
                                    value="{{ $guests }} người"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    {{-- ===== CASE 1: KHÔNG CÓ BÀN TRỐNG ===== --}}
                    @if($availableTables->isEmpty())

                    <div class="alert alert-warning d-flex align-items-start gap-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Không còn bàn trống!</h6>
                            <p class="mb-2">
                                Rất tiếc, nhà hàng không còn bàn trống vào
                                <strong>{{ $time }} ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
                                cho <strong>{{ $guests }} khách</strong>.
                            </p>
                            <p class="mb-0 small">Vui lòng thử chọn thời gian khác hoặc giảm số lượng khách.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Quay lại & chọn thời gian khác
                        </a>
                        <a href="{{ route('restaurants.index') }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            Tìm nhà hàng khác
                        </a>
                    </div>

                    {{-- ===== CASE 2: CÓ BÀN TRỐNG ===== --}}
                    @else

                    <form action="{{ route('bookings.store') }}"
                        method="POST"
                        id="booking-form">
                        @csrf

                        {{-- Hidden fields --}}
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        <input type="hidden" name="booking_date" value="{{ $date }}">
                        <input type="hidden" name="booking_time" value="{{ $time }}">
                        <input type="hidden" name="guests" value="{{ $guests }}">

                        {{-- Chọn bàn --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-layout-three-columns me-1 text-primary"></i>
                                Chọn bàn
                                <span class="badge bg-primary rounded-pill ms-1">
                                    {{ $availableTables->count() }} bàn trống
                                </span>
                            </h6>

                            @error('table_id')
                            <div class="alert alert-danger py-2 mb-3">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                            @enderror

                            <div class="row g-3" id="table-selection">
                                @foreach($availableTables as $table)
                                <div class="col-sm-6 col-md-4">
                                    <input type="radio"
                                        class="btn-check"
                                        name="table_id"
                                        id="table_{{ $table->id }}"
                                        value="{{ $table->id }}"
                                        {{ old('table_id') == $table->id ? 'checked' : '' }}
                                        required>
                                    <label class="btn btn-outline-secondary w-100 h-100 table-card p-0"
                                        for="table_{{ $table->id }}">
                                        <div class="p-3 text-start">

                                            {{-- Badge loại bàn --}}
                                            <div class="mb-2">
                                                @switch($table->type ?? 'standard')
                                                @case('vip')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-star-fill me-1"></i>VIP
                                                </span>
                                                @break
                                                @case('outdoor')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-tree me-1"></i>Ngoài trời
                                                </span>
                                                @break
                                                @case('private')
                                                <span class="badge bg-purple" style="background:#6f42c1">
                                                    <i class="bi bi-shield-lock me-1"></i>Riêng tư
                                                </span>
                                                @break
                                                @default
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-grid me-1"></i>Thường
                                                </span>
                                                @endswitch
                                            </div>

                                            {{-- Tên bàn --}}
                                            <div class="fw-bold fs-6 mb-1">
                                                {{ $table->name }}
                                            </div>

                                            {{-- Sức chứa --}}
                                            <div class="text-muted small">
                                                <i class="bi bi-people-fill me-1"></i>
                                                Sức chứa: <strong>{{ $table->capacity }} người</strong>
                                            </div>

                                            {{-- Cảnh báo nếu bàn hơi nhỏ --}}
                                            @if($table->capacity == $guests)
                                            <div class="mt-2">
                                                <span class="badge bg-info text-dark small">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Vừa đủ
                                                </span>
                                            </div>
                                            @elseif($table->capacity >= $guests + 2)
                                            <div class="mt-2">
                                                <span class="badge bg-light text-muted border small">
                                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                                    Còn {{ $table->capacity - $guests }} chỗ dư
                                                </span>
                                            </div>
                                            @endif

                                        </div>

                                        {{-- Checkmark khi chọn --}}
                                        <div class="table-selected-icon">
                                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Ghi chú --}}
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">
                                <i class="bi bi-chat-square-text me-1 text-primary"></i>
                                Ghi chú <span class="text-muted fw-normal">(không bắt buộc)</span>
                            </label>
                            <textarea name="notes"
                                id="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                rows="3"
                                maxlength="500"
                                placeholder="Ví dụ: Cần ghế cho trẻ em, dị ứng thực phẩm, trang trí sinh nhật...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-end">
                                <span id="notes-count">0</span>/500 ký tự
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-between align-items-center">
                            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                                class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Quay lại
                            </a>
                            <button type="submit"
                                class="btn btn-primary btn-lg px-5"
                                id="submit-btn">
                                <i class="bi bi-calendar-check me-2"></i>
                                Xác nhận đặt bàn
                            </button>
                        </div>

                    </form>

                    @endif
                    {{-- end if availableTables --}}

                </div>
            </div>

        </div>
        {{-- end col-lg-8 --}}

        {{-- ===== CỘT PHẢI: THÔNG TIN NHÀ HÀNG ===== --}}
        <div class="col-lg-4">

            {{-- Card nhà hàng --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    @if($restaurant->image)
                    <img src="{{ asset('storage/' . $restaurant->image) }}"
                        alt="{{ $restaurant->name }}"
                        class="img-fluid rounded-3 mb-3 w-100"
                        style="height: 160px; object-fit: cover;">
                    @endif
                    <h6 class="fw-bold">{{ $restaurant->name }}</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $restaurant->address }}
                    </p>
                    @if($restaurant->phone)
                    <p class="text-muted small mb-2">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $restaurant->phone }}
                    </p>
                    @endif
                    @if($restaurant->rating_avg ?? false)
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span class="fw-semibold">{{ number_format($restaurant->rating_avg, 1) }}</span>
                        <span class="text-muted small">({{ $restaurant->reviews_count ?? 0 }} đánh giá)</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Chính sách đặt bàn --}}
            <div class="card border-0 bg-light rounded-3">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Chính sách đặt bàn
                    </h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check2 text-success me-2"></i>
                            Xác nhận qua email & SMS
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check2 text-success me-2"></i>
                            Giữ bàn trong 15 phút
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check2 text-success me-2"></i>
                            Hủy miễn phí trước 2 giờ
                        </li>
                        <li>
                            <i class="bi bi-check2 text-success me-2"></i>
                            Không phí đặt bàn
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        {{-- end col-lg-4 --}}

    </div>
</div>
@endsection

@push('styles')
<style>
    /* Card bàn */
    .table-card {
        border: 2px solid #dee2e6 !important;
        border-radius: 12px !important;
        transition: all .2s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-check:checked+.table-card {
        border-color: #0d6efd !important;
        background-color: #f0f5ff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, .15);
    }

    .table-card:hover {
        border-color: #86b7fe !important;
        background-color: #f8f9ff !important;
    }

    /* Checkmark icon - ẩn mặc định */
    .table-selected-icon {
        display: none;
        position: absolute;
        top: 8px;
        right: 8px;
    }

    .btn-check:checked+.table-card .table-selected-icon {
        display: block;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Đếm ký tự ghi chú
        const notes = document.getElementById('notes');
        const counter = document.getElementById('notes-count');

        if (notes && counter) {
            const update = () => {
                counter.textContent = notes.value.length;
                counter.classList.toggle('text-danger', notes.value.length > 450);
            };
            notes.addEventListener('input', update);
            update();
        }

        // Disable nút submit sau khi click (chống double-submit)
        const form = document.getElementById('booking-form');
        const submitBtn = document.getElementById('submit-btn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            });
        }

    });
</script>
@endpush