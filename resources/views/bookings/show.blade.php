@extends('layouts.app')

@section('title', 'Chi tiết đặt bàn #' . str_pad($booking->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('bookings.index') }}">Lịch sử đặt bàn</a>
            </li>
            <li class="breadcrumb-item active">
                #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
            </li>
        </ol>
    </nav>

    @php
        $badgeConfig = [
            'pending'   => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split',  'label' => 'Chờ xác nhận'],
            'confirmed' => ['bg' => 'bg-success text-white','icon' => 'bi-check-circle-fill', 'label' => 'Đã xác nhận'],
            'cancelled' => ['bg' => 'bg-danger text-white', 'icon' => 'bi-x-circle-fill',    'label' => 'Đã hủy'],
            'completed' => ['bg' => 'bg-secondary text-white','icon' => 'bi-flag-fill',       'label' => 'Hoàn thành'],
        ];
        $cfg = $badgeConfig[$booking->status] ?? $badgeConfig['pending'];
    @endphp

    <div class="row g-4">

        {{-- ===== CỘT TRÁI ===== --}}
        <div class="col-lg-8">

            {{-- Card thông tin đặt bàn --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small">Mã đặt bàn</span>
                            <h5 class="mb-0 fw-bold">
                                #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </h5>
                        </div>
                        <span class="badge {{ $cfg['bg'] }} fs-6 rounded-pill px-3 py-2">
                            <i class="{{ $cfg['icon'] }} me-1"></i>
                            {{ $cfg['label'] }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">

                    {{-- Nhà hàng --}}
                    <div class="d-flex gap-3 align-items-start mb-4 pb-4 border-bottom">
                        @if($booking->restaurant->image)
                            <img src="{{ asset('storage/' . $booking->restaurant->image) }}"
                                 alt="{{ $booking->restaurant->name }}"
                                 class="rounded-3 flex-shrink-0"
                                 style="width:80px; height:80px; object-fit:cover;">
                        @else
                            <div class="bg-primary bg-opacity-10 rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:80px; height:80px">
                                <i class="bi bi-shop text-primary fs-2"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="fw-bold mb-1">{{ $booking->restaurant->name }}</h5>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $booking->restaurant->address }}
                            </p>
                            @if($booking->restaurant->phone)
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $booking->restaurant->phone }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Chi tiết booking --}}
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-calendar3 text-primary fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Ngày đặt bàn</div>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->isoFormat('dddd') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-clock text-success fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Giờ đặt bàn</div>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-people text-info fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Số khách</div>
                                    <div class="fw-semibold">{{ $booking->guests }} người</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-layout-three-columns text-warning fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Bàn đã đặt</div>
                                    <div class="fw-semibold">{{ $booking->table->name ?? 'N/A' }}</div>
                                    @if($booking->table)
                                        <div class="text-muted small">
                                            Sức chứa: {{ $booking->table->capacity }} người
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ghi chú --}}
                    @if($booking->notes)
                        <div class="bg-light rounded-3 p-3">
                            <div class="text-muted small fw-semibold mb-1">
                                <i class="bi bi-chat-square-text me-1"></i>
                                Ghi chú
                            </div>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ===== TIMELINE ===== --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>
                        Trạng thái đơn đặt bàn
                    </h6>
                </div>
                <div class="card-body p-4">

                    @php
                        // Xác định step hiện tại
                        $steps = [
                            'created'   => 0,
                            'pending'   => 1,
                            'confirmed' => 2,
                            'completed' => 3,
                            'cancelled' => -1, // special
                        ];
                        $currentStep = $steps[$booking->status] ?? 1;
                        $isCancelled = $booking->status === 'cancelled';
                    @endphp

                    <div class="timeline">

                        {{-- Step 1: Đã tạo --}}
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success">
                                <i class="bi bi-check-lg text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="fw-semibold">Đặt bàn thành công</div>
                                <div class="text-muted small">
                                    {{ $booking->created_at->format('H:i, d/m/Y') }}
                                </div>
                                <div class="text-muted small">
                                    Yêu cầu đặt bàn của bạn đã được ghi nhận
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Chờ xác nhận --}}
                        <div class="timeline-item {{ $currentStep >= 1 && !$isCancelled ? 'completed' : ($isCancelled ? 'cancelled' : 'pending') }}">
                            <div class="timeline-marker
                                {{ $currentStep >= 1 && !$isCancelled ? 'bg-success' : ($isCancelled ? 'bg-danger' : 'bg-light border') }}">
                                @if($isCancelled)
                                    <i class="bi bi-x-lg text-white"></i>
                                @elseif($currentStep >= 1)
                                    <i class="bi bi-check-lg text-white"></i>
                                @else
                                    <i class="bi bi-hourglass-split text-muted"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                @if($isCancelled)
                                    <div class="fw-semibold text-danger">Đơn đã bị hủy</div>
                                    <div class="text-muted small">
                                        {{ $booking->cancelled_at?->format('H:i, d/m/Y') ?? 'N/A' }}
                                    </div>
                                    <div class="text-muted small">
                                        Đơn đặt bàn đã được hủy thành công
                                    </div>
                                @else
                                    <div class="fw-semibold {{ $currentStep >= 1 ? '' : 'text-muted' }}">
                                        Chờ xác nhận
                                    </div>
                                    @if($booking->status === 'pending')
                                        <div class="text-muted small">Đang chờ nhà hàng xác nhận...</div>
                                    @elseif($currentStep >= 2)
                                        <div class="text-muted small">
                                            Nhà hàng đã nhận được yêu cầu
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Step 3: Kết quả (Đã xác nhận / Hoàn thành) --}}
                        @if(!$isCancelled)
                            <div class="timeline-item {{ $currentStep >= 2 ? 'completed' : 'pending' }}">
                                <div class="timeline-marker
                                    {{ $currentStep >= 2 ? 'bg-success' : 'bg-light border' }}">
                                    @if($currentStep >= 2)
                                        <i class="bi bi-check-lg text-white"></i>
                                    @else
                                        <i class="bi bi-circle text-muted"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="fw-semibold {{ $currentStep >= 2 ? 'text-success' : 'text-muted' }}">
                                        {{ $booking->status === 'completed' ? 'Hoàn thành' : 'Đã xác nhận' }}
                                    </div>
                                    @if($currentStep >= 2)
                                        <div class="text-muted small">
                                            @if($booking->status === 'completed')
                                                Cảm ơn bạn đã sử dụng dịch vụ!
                                            @else
                                                Nhà hàng đã xác nhận đặt bàn của bạn
                                            @endif
                                        </div>
                                        @if($booking->confirmed_at)
                                            <div class="text-muted small">
                                                {{ $booking->confirmed_at->format('H:i, d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-muted small">Chờ nhà hàng xác nhận</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                    {{-- end timeline --}}

                </div>
            </div>

            {{-- ===== HỦY ĐƠN ===== --}}
            @if($canCancel)
                <div class="card border-danger border-opacity-50 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold text-danger mb-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Hủy đặt bàn
                        </h6>
                        <p class="text-muted small mb-3">
                            Sau khi hủy, bạn sẽ cần đặt bàn mới nếu muốn quay lại.
                            Hủy miễn phí trước giờ đặt ít nhất 2 tiếng.
                        </p>
                        <form action="{{ route('bookings.cancel', $booking->id) }}"
                              method="POST"
                              id="cancel-form">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="cancel_reason" class="form-label small fw-semibold">
                                    Lý do hủy <span class="text-muted fw-normal">(không bắt buộc)</span>
                                </label>
                                <textarea name="cancel_reason"
                                          id="cancel_reason"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Nhập lý do hủy đặt bàn..."></textarea>
                            </div>

                            <button type="button"
                                    class="btn btn-outline-danger"
                                    id="cancel-btn">
                                <i class="bi bi-x-circle me-1"></i>
                                Hủy đặt bàn này
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- ===== ĐÁNH GIÁ ===== --}}
            @if($canReview)
                <div class="card border-0 bg-warning bg-opacity-10 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h6 class="fw-semibold mb-1">
                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                    Đánh giá trải nghiệm
                                </h6>
                                <p class="text-muted small mb-0">
                                    Chia sẻ cảm nhận của bạn để giúp cộng đồng có thêm thông tin.
                                </p>
                            </div>
                            <a href="{{ route('restaurants.show', $booking->restaurant->slug) }}?tab=reviews&booking={{ $booking->id }}"
                               class="btn btn-warning text-white flex-shrink-0">
                                <i class="bi bi-pencil-square me-1"></i>
                                Viết đánh giá ngay
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        {{-- end col-lg-8 --}}

        {{-- ===== CỘT PHẢI ===== --}}
        <div class="col-lg-4">

            {{-- Tóm tắt --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4 sticky-top" style="top: 80px">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-semibold mb-0">Tóm tắt đặt bàn</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Mã đơn</span>
                            <span class="fw-semibold small">
                                #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Nhà hàng</span>
                            <span class="fw-semibold small text-end" style="max-width:160px">
                                {{ $booking->restaurant->name }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Ngày</span>
                            <span class="fw-semibold small">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Giờ</span>
                            <span class="fw-semibold small">
                                {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Số khách</span>
                            <span class="fw-semibold small">{{ $booking->guests }} người</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">Bàn</span>
                            <span class="fw-semibold small">{{ $booking->table->name ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between pt-2">
                            <span class="text-muted small">Trạng thái</span>
                            <span class="badge {{ $cfg['bg'] }} small">{{ $cfg['label'] }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Actions --}}
                <div class="card-footer bg-white border-top p-4 d-grid gap-2">
                    <a href="{{ route('restaurants.show', $booking->restaurant->slug) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-shop me-1"></i>
                        Xem nhà hàng
                    </a>
                    <a href="{{ route('bookings.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>
                        Tất cả đơn đặt bàn
                    </a>
                </div>
            </div>

        </div>
        {{-- end col-lg-4 --}}

    </div>
</div>

{{-- ===== MODAL XÁC NHẬN HỦY ===== --}}
@if($canCancel)
    <div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Xác nhận hủy đặt bàn
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Bạn có chắc chắn muốn hủy đơn đặt bàn
                        <strong>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong>
                        tại <strong>{{ $booking->restaurant->name }}</strong> không?
                    </p>
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Hành động này không thể hoàn tác.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Không, giữ lại
                    </button>
                    <button type="button"
                            class="btn btn-danger"
                            id="confirm-cancel-btn">
                        <i class="bi bi-x-circle me-1"></i>
                        Xác nhận hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
/* ===== TIMELINE ===== */
.timeline {
    position: relative;
    padding-left: 0;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    position: relative;
    padding-bottom: 1.5rem;
}

/* Đường kẻ nối các bước */
.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item.completed:not(:last-child)::before {
    background-color: #198754;
}

/* Marker tròn */
.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
    z-index: 1;
    border: 2px solid transparent;
}

.timeline-marker.bg-light {
    border-color: #dee2e6 !important;
}

.timeline-content {
    padding-top: 0.4rem;
    flex: 1;
}

.timeline-item.pending .timeline-content {
    opacity: 0.5;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelBtn        = document.getElementById('cancel-btn');
    if (canCancel) {
        const confirmCancelBtn = document.getElementById('confirm-cancel-btn');
        const cancelForm       = document.getElementById('cancel-form');
        const modal            = new bootstrap.Modal(document.getElementById('confirmCancelModal'));

        // Mở modal khi click nút hủy
        cancelBtn?.addEventListener('click', function () {
            modal.show();
        });

        // Submit form khi xác nhận trong modal
        confirmCancelBtn?.addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang hủy...';
            cancelForm.submit();
        });
    }

});
</script>
@endpush