@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- TIÊU ĐỀ KẾT QUẢ TÌM KIẾM -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}" class="text-decoration-none">Nhà
                        hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark">
            Kết quả tìm kiếm cho: <span class="text-primary">"{{ request('q') }}"</span>
        </h2>
        <p class="text-muted">Tìm thấy {{ $restaurants->total() }} nhà hàng khớp với từ khóa của bạn.</p>
    </div>

    <!-- THANH FILTER & SORT (Nhúng lại bộ lọc từ index để đồng bộ trải nghiệm) -->
    @include('restaurants.partials.filter_bar')
    <!-- Bạn có thể tách form lọc ở index ra partial nếu muốn gọn -->

    <!-- GRID KẾT QUẢ TÌM KIẾM -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
        @php
        // Hàm helper inline an toàn để highlight từ khóa không phân biệt hoa thường
        function highlightKeyword($text, $searchQuery) {
        if (empty($searchQuery)) {
        return e($text); // Xử lý escape HTML an toàn chống XSS
        }
        $escapedQuery = preg_quote($searchQuery, '/');
        // Sử dụng class bg-warning và p-0 của Bootstrap 5 để highlight đẹp mắt
        return preg_replace('/(' . $escapedQuery . ')/iu', '<mark class="p-0 bg-warning text-dark fw-bold">$1</mark>',
        e($text));
        }
        @endphp

        @forelse($restaurants as $restaurant)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all position-relative">

                <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;">
                    <img src="{{ $restaurant->image ?? 'https://placehold.co/600x450?text=No+Image' }}"
                        class="w-100 h-100 object-fit-cover" alt="{{ $restaurant->name }}">
                    <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 py-2 px-3 fs-7">
                        {{ $restaurant->category->name }}
                    </span>
                </div>

                <div class="card-body d-flex flex-column p-4">
                    <!-- TÊN NHÀ HÀNG ĐƯỢC HIGHLIGHT TỪ KHÓA -->
                    <h5 class="card-title fw-bold mb-2">
                        <a href="{{ url('/restaurants/' . $restaurant->id) }}"
                            class="text-decoration-none text-dark link-primary">
                            {!! highlightKeyword($restaurant->name, request('q')) !!}
                        </a>
                    </h5>

                    <!-- ĐỊA CHỈ ĐƯỢC HIGHLIGHT TỪ KHÓA (nếu muốn) -->
                    <p class="card-text text-muted small mb-3 text-truncate">
                        📍 {!! highlightKeyword($restaurant->address . ', ' . $restaurant->city, request('q')) !!}
                    </p>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                        <div>
                            <span class="text-warning fw-bold">★</span>
                            <span class="fw-bold text-dark">{{ round($restaurant->average_rating, 1) ?? '0.0' }}</span>
                        </div>
                        <div class="small text-muted">
                            🕒 {{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}
                        </div>
                    </div>
                </div>

                <div class="position-absolute bottom-0 end-0 m-4 mb-5 pb-2">
                    <span class="badge bg-light text-success border border-success border-opacity-25">
                        {{ $restaurant->price_range > 1000000 ? '$$$' : ($restaurant->price_range > 300000 ? '$$' : '$') }}
                    </span>
                </div>

            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="mb-3 fs-1 text-muted">🔍</div>
            <h4 class="fw-bold text-secondary">Không tìm thấy nhà hàng nào với từ khóa "{{ request('q') }}"</h4>
            <p class="text-muted">Thử tìm kiếm với tên thành phố khác hoặc kiểm tra lại chính tả nhé.</p>
            <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-sm mt-2">Quay lại danh sách</a>
        </div>
        @endforelse
    </div>

    <!-- PHÂN TRANG -->
    <div class="d-flex justify-content-center mt-5">
        {{ $restaurants->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection