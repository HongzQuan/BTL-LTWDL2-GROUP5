@extends('layouts.app')

@section('title', 'Lịch sử đặt bàn')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-calendar2-week me-2 text-primary"></i>
                Lịch sử đặt bàn
            </h4>
            <p class="text-muted small mb-0">
                Quản lý tất cả đơn đặt bàn của bạn
            </p>
        </div>
        <a href="{{ route('restaurants.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Đặt bàn mới
        </a>
    </div>

    {{-- ===== FILTER PILLS ===== --}}
    <ul class="nav nav-pills gap-2 mb-4 flex-wrap">

        @php
            $currentStatus = request('status', '');

            $filters = [
                ''          => ['label' => 'Tất cả',          'icon' => 'bi-list-ul'],
                'pending'   => ['label' => 'Chờ xác nhận',    'icon' => 'bi-hourglass-split'],
                'confirmed' => ['label' => 'Đã xác nhận',     'icon' => 'bi-check-circle'],
                'cancelled' => ['label' => 'Đã hủy',          'icon' => 'bi-x-circle'],
                'completed' => ['label' => 'Hoàn thành',      'icon' => 'bi-flag-fill'],
            ];

            $pillColors = [
                ''          => 'btn-outline-secondary',
                'pending'   => 'btn-outline-warning',
                'confirmed' => 'btn-outline-success',
                'cancelled' => 'btn-outline-danger',
                'completed' => 'btn-outline-dark',
            ];

            $pillActiveColors = [
                ''          => 'btn-secondary',
                'pending'   => 'btn-warning',
                'confirmed' => 'btn-success',
                'cancelled' => 'btn-danger',
                'completed' => 'btn-dark',
            ];
        @endphp

        @foreach($filters as $value => $info)
            <li class="nav-item">
                <a href="{{ route('bookings.index', array_filter(['status' => $value])) }}"
                   class="btn btn-sm rounded-pill
                          {{ $currentStatus === $value
                              ? $pillActiveColors[$value] . ' text-white'
                              : $pillColors[$value] }}">
                    <i class="{{ $info['icon'] }} me-1"></i>
                    {{ $info['label'] }}
                </a>
            </li>
        @endforeach

    </ul>

    {{-- ===== DANH SÁCH BOOKING ===== --}}
    @if($bookings->isEmpty())

        {{-- Empty state --}}
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem; opacity:.4"></i>
            </div>
            @if($currentStatus !== '')
                <h5 class="text-muted fw-semibold">
                    Không có đơn đặt bàn nào
                    @switch($currentStatus)
                        @case('pending')   ở trạng thái <span class="text-warning">Chờ xác nhận</span> @break
                        @case('confirmed') ở trạng thái <span class="text-success">Đã xác nhận</span>   @break
                        @case('cancelled') ở trạng thái <span class="text-danger">Đã hủy</span>         @break
                        @case('completed') ở trạng thái <span class="text-secondary">Hoàn thành</span>  @break
                    @endswitch
                </h5>
                <p class="text-muted small mb-4">
                    Thử chọn trạng thái khác hoặc xem tất cả đơn đặt bàn.
                </p>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-list-ul me-1"></i>Xem tất cả
                </a>
            @else
                <h5 class="text-muted fw-semibold">Bạn chưa có đơn đặt bàn nào</h5>
                <p class="text-muted small mb-4">
                    Khám phá các nhà hàng và đặt bàn ngay hôm nay!
                </p>
            @endif
            <a href="{{ route('restaurants.index') }}" class="btn btn-primary">
                <i class="bi bi-search me-1"></i>
                Tìm nhà hàng
            </a>
        </div>

    @else

        <div class="list-group shadow-sm rounded-3 mb-4">
            @foreach($bookings as $booking)

                @php
                    // Config badge theo status
                    $badgeConfig = [
                        'pending'   => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split', 'label' => 'Chờ xác nhận'],
                        'confirmed' => ['bg' => 'bg-success text-white', 'icon' => 'bi-check-circle-fill', 'label' => 'Đã xác nhận'],
                        'cancelled' => ['bg' => 'bg-danger text-white',  'icon' => 'bi-x-circle-fill',    'label' => 'Đã hủy'],
                        'completed' => ['bg' => 'bg-secondary text-white','icon' => 'bi-flag-fill',        'label' => 'Hoàn thành'],
                    ];
                    $cfg = $badgeConfig[$booking->status] ?? $badgeConfig['pending'];
                @endphp

                <div class="list-group-item list-group-item-action px-4 py-3 border-start-0 border-end-0">
                    <div class="row align-items-center g-3">

                        {{-- Thông tin chính --}}
                        <div class="col-md-7">
                            <div class="d-flex align-items-start gap-3">

                                {{-- Icon nhà hàng --}}
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:48px; height:48px">
                                        <i class="bi bi-shop text-primary fs-5"></i>
                                    </div>
                                </div>

                                <div>
                                    {{-- Mã đơn + badge --}}
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-muted small fw-semibold">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="badge {{ $cfg['bg'] }} rounded-pill">
                                            <i class="{{ $cfg['icon'] }} me-1"></i>
                                            {{ $cfg['label'] }}
                                        </span>
                                    </div>

                                    {{-- Tên nhà hàng --}}
                                    <div class="fw-bold mb-1">
                                        {{ $booking->restaurant->name }}
                                    </div>

                                    {{-- Địa chỉ --}}
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ Str::limit($booking->restaurant->address, 50) }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Chi tiết booking --}}
                        <div class="col-md-3">
                            <div class="small">
                                <div class="mb-1">
                                    <i class="bi bi-calendar3 me-2 text-muted"></i>
                                    <strong>
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                    </strong>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-clock me-2 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                </div>
                                <div>
                                    <i class="bi bi-people me-2 text-muted"></i>
                                    {{ $booking->guests }} khách
                                </div>
                            </div>
                        </div>

                        {{-- Nút hành động --}}
                        <div class="col-md-2 text-md-end">
                            <a href="{{ route('bookings.show', $booking->id) }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i>
                                Chi tiết
                            </a>
                        </div>

                    </div>
                </div>

            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Hiển thị {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }}
                trong tổng số {{ $bookings->total() }} đơn đặt bàn
            </div>
            <div>
                {{ $bookings->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>

    @endif

</div>
@endsection