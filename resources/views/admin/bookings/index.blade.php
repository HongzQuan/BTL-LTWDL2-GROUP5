@extends('layouts.admin')

@section('title', 'Quản lý đặt bàn')

@section('content')
<div class="container-fluid py-4">

    {{-- ════════════════════════════════════════════════════════════
         TIÊU ĐỀ
    ════════════════════════════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-calendar2-check me-2 text-primary"></i>Quản lý đặt bàn
        </h4>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ════════════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         THỐNG KÊ NHANH — 4 CARD
    ════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Pending --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-15 p-3">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Chờ xác nhận</p>
                        <h4 class="fw-bold mb-0 text-warning">{{ $statSummary['pending'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Confirmed --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-15 p-3">
                        <i class="bi bi-calendar-check fs-4 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Đã xác nhận</p>
                        <h4 class="fw-bold mb-0 text-primary">{{ $statSummary['confirmed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Completed --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-15 p-3">
                        <i class="bi bi-check2-all fs-4 text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Hoàn thành</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $statSummary['completed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- Cancelled --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-15 p-3">
                        <i class="bi bi-x-circle fs-4 text-danger"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Đã hủy</p>
                        <h4 class="fw-bold mb-0 text-danger">{{ $statSummary['cancelled'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         THANH FILTER
    ════════════════════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">

                    {{-- Nhà hàng --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-shop me-1"></i>Nhà hàng
                        </label>
                        <select name="restaurant_id" class="form-select form-select-sm">
                            <option value="">— Tất cả nhà hàng —</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}"
                                    {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                    {{ $restaurant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-funnel me-1"></i>Trạng thái
                        </label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">— Tất cả —</option>
                            <option value="pending"   {{ request('status') === 'pending'    ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') === 'confirmed'  ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') === 'completed'  ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') === 'cancelled'  ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-calendar-event me-1"></i>Từ ngày
                        </label>
                        <input type="date"
                               name="date_from"
                               class="form-control form-control-sm"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-calendar-event me-1"></i>Đến ngày
                        </label>
                        <input type="date"
                               name="date_to"
                               class="form-control form-control-sm"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- Tìm kiếm --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-search me-1"></i>Tìm khách hàng
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Tên hoặc số điện thoại..."
                                   value="{{ request('q') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Reset --}}
                @if(request()->hasAny(['restaurant_id','status','date_from','date_to','q']))
                    <div class="mt-2">
                        <a href="{{ route('admin.bookings.index') }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                        </a>
                        <span class="text-muted small ms-2">
                            Đang lọc — {{ $bookings->total() }} kết quả
                        </span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         BẢNG DỮ LIỆU
    ════════════════════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
                Tổng cộng: <strong>{{ $bookings->total() }}</strong> đơn
            </span>
            <span class="text-muted small">
                Trang {{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" width="50">STT</th>
                            <th width="80">Mã đơn</th>
                            <th>Tên khách</th>
                            <th>SĐT</th>
                            <th>Nhà hàng</th>
                            <th>Bàn</th>
                            <th>Ngày giờ đặt</th>
                            <th class="text-center" width="80">Số khách</th>
                            <th class="text-center" width="120">Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-center" width="160">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $index => $booking)
                        <tr>
                            {{-- STT --}}
                            <td class="ps-3 text-muted">
                                {{ $bookings->firstItem() + $index }}
                            </td>

                            {{-- Mã đơn --}}
                            <td>
                                <span class="fw-semibold text-primary">#{{ $booking->id }}</span>
                            </td>

                            {{-- Tên khách --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm rounded-circle bg-primary bg-opacity-10
                                                d-flex align-items-center justify-content-center"
                                         style="width:32px;height:32px;flex-shrink:0">
                                        <span class="text-primary fw-bold small">
                                            {{ strtoupper(substr($booking->user->name ?? 'K', 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="fw-semibold small">
                                        {{ $booking->user->name ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- SĐT --}}
                            <td class="small text-muted">
                                {{ $booking->user->phone ?? '—' }}
                            </td>

                            {{-- Nhà hàng --}}
                            <td>
                                <span class="small text-dark fw-semibold">
                                    {{ $booking->restaurant->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Bàn --}}
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 small">
                                    {{ $booking->table->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Ngày giờ đặt --}}
                            <td class="small">
                                <div class="fw-semibold">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : '—' }}
                                </div>
                                <div class="text-muted text-xs">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('H:i') : '—' }}
                                </div>
                            </td>

                            {{-- Số khách --}}
                            <td class="text-center fw-bold text-secondary">
                                {{ $booking->guests_count ?? 0 }}
                            </td>

                            {{-- Trạng thái --}}
                            <td class="text-center">
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark px-2 py-1.5 small">Chờ xác nhận</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-primary px-2 py-1.5 small">Đã xác nhận</span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge bg-success px-2 py-1.5 small">Hoàn thành</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="badge bg-danger px-2 py-1.5 small">Đã hủy</span>
                                @endif
                            </td>

                            {{-- Ngày tạo --}}
                            <td class="small text-muted">
                                {{ $booking->created_at ? $booking->created_at->format('d/m/Y H:i') : '—' }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($booking->status === 'pending')
                                        {{-- Nút Xác nhận (Xanh) --}}
                                        <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline status-change-form" data-message="Bạn có chắc chắn muốn XÁC NHẬN đơn đặt bàn #{{ $booking->id }} này không?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="Xác nhận đơn">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        {{-- Nút Hủy (Đỏ) --}}
                                        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline status-change-form" data-message="Bạn có chắc chắn muốn HỦY đơn đặt bàn #{{ $booking->id }} này không?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hủy đơn">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>

                                    @elseif($booking->status === 'confirmed')
                                        {{-- Nút Hoàn thành (Xám) --}}
                                        @php
                                            // Sửa logic lte(now) thay vì today() để tính cả giờ phút thực tế
                                            $canComplete = $booking->booking_date && \Carbon\Carbon::parse($booking->booking_date)->lte(\Carbon\Carbon::now());
                                        @endphp
                                        <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST" class="d-inline status-change-form" data-message="Chuyển đơn đặt bàn #{{ $booking->id }} sang trạng thái HOÀN THÀNH?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-secondary px-2 py-1" title="Hoàn thành đơn" {{ !$canComplete ? 'disabled' : '' }}>
                                                <i class="bi bi-calendar2-check-fill"></i>
                                            </button>
                                        </form>

                                        {{-- Nút Hủy (Đỏ) --}}
                                        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline status-change-form" data-message="Bạn có chắc chắn muốn HỦY đơn đặt bàn #{{ $booking->id }} này không?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hủy đơn">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>

                                        {{-- Nút Hủy (Đỏ) --}}
                                        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline status-change-form" data-message="Bạn có chắc chắn muốn HỦY đơn đặt bàn #{{ $booking->id }} này không?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hủy đơn">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Các status khác: chỉ hiện dấu gạch ngang nhẹ nhàng --}}
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                Không tìm thấy dữ liệu đặt bàn nào trùng khớp.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Thanh phân trang phía dưới bảng --}}
        @if($bookings->hasPages())
            <div class="card-footer bg-white py-3 border-top-0">
                <div class="d-flex justify-content-center">
                    {{ $bookings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     JAVASCRIPT CONFIRM DIALOG BEFORE SUBMIT
════════════════════════════════════════════════════════════ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.status-change-form');
        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Chặn việc submit form ngay lập tức
                const message = this.getAttribute('data-message') || "Bạn có chắc chắn muốn thực hiện thao tác này?";
                
                if (confirm(message)) {
                    this.submit(); // Thực hiện submit form thực tế khi nhấn OK
                }
            });
        });
    });
</script>

@endsection