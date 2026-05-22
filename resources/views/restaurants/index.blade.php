<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách Nhà hàng</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <!-- SIDEBAR: Filter & Sort -->
            <div class="col-md-3">
                <form action="{{ route('restaurants.index') }}" method="GET">
                    <!-- Giữ lại từ khóa tìm kiếm nếu có -->
                    @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <h5 class="mb-3">Bộ lọc</h5>
                    <select name="city" class="form-select mb-3">
                        <option value="">-- Chọn Tỉnh/Thành --</option>
                        @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}
                        </option>
                        @endforeach
                    </select>

                    <select name="sort" class="form-select mb-3" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Lọc kết quả</button>
                </form>
            </div>

            <!-- CONTENT: Danh sách nhà hàng -->
            <div class="col-md-9">
                <!-- Form Tìm kiếm riêng (Trỏ vào hàm search) -->
                <form action="{{ url('/restaurants/search') }}" method="GET" class="d-flex mb-4">
                    <input type="text" name="q" class="form-control me-2" value="{{ request('q') }}"
                        placeholder="Nhập tên hoặc địa chỉ..." required>
                    <button class="btn btn-outline-success" type="submit">Tìm kiếm</button>
                </form>

                <div class="row">
                    @php
                    // Hàm helper nhỏ để highlight từ khóa bằng regex
                    function highlight($text, $term) {
                    if (!$term) return htmlspecialchars($text);
                    $escapedTerm = preg_quote($term, '/');
                    // Dùng thẻ <mark> của Bootstrap để bôi vàng
                        return preg_replace('/(' . $escapedTerm . ')/iu', '<mark class="p-0 bg-warning">$1</mark>',
                        htmlspecialchars($text));
                        }
                        @endphp

                        @forelse($restaurants as $restaurant)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $restaurant->image }}" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <!-- In ra tên đã highlight -->
                                    <h5 class="card-title">
                                        {!! highlight($restaurant->name, request('q')) !!}
                                    </h5>
                                    <p class="card-text text-muted small">
                                        {!! highlight($restaurant->address, request('q')) !!}
                                    </p>
                                    <p class="text-danger fw-bold">{{ number_format($restaurant->price_range) }} VNĐ</p>
                                    <span class="badge bg-info">{{ $restaurant->category->name }}</span>
                                    <span class="badge bg-warning text-dark">⭐
                                        {{ round($restaurant->average_rating, 1) }}</span>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <a href="{{ route('restaurants.show', $restaurant->id) }}"
                                        class="btn btn-primary w-100">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-warning">Không tìm thấy nhà hàng nào!</div>
                        </div>
                        @endforelse
                </div>

                <!-- Phân trang Bootstrap 5 -->
                <div class="mt-3">
                    {{ $restaurants->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    @extends('layouts.app')

    @section('content')
    <div class="container py-4">

        <!-- THANH FILTER NGANG -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('restaurants.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Giữ lại trạng thái sort hiện tại nếu có -->
                    @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- Thành phố -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Thành phố</label>
                        <select name="city" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Danh mục -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Khoảng giá Min -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Giá tối thiểu</label>
                        <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}"
                            placeholder="VD: 100000">
                    </div>

                    <!-- Khoảng giá Max -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Giá tối đa</label>
                        <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}"
                            placeholder="VD: 2000000">
                    </div>

                    <!-- Ô tìm kiếm nhanh -->
                    <div class="col-md-3 col-sm-8">
                        <label class="form-label fw-semibold small text-muted">Từ khóa</label>
                        <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                            placeholder="Tên hoặc địa chỉ...">
                    </div>

                    <!-- Nút áp dụng -->
                    <div class="col-md-1 col-sm-4">
                        <button type="submit" class="btn btn-primary w-100">
                            Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- THANH SẮP XẾP & THÔNG TIN KẾT QUẢ -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <p class="text-muted mb-0">
                    Tìm thấy <strong class="text-dark">{{ $restaurants->total() }}</strong> nhà hàng phù hợp
                </p>
            </div>

            <!-- Nhóm nút Sort mượt mà bằng việc giữ lại các params cũ -->
            <div class="btn-group shadow-sm" role="group" aria-label="Sắp xếp nhà hàng">
                @php
                $currentParams = request()->except('sort');
                @endphp
                <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'rating'])) }}"
                    class="btn btn-outline-secondary btn-sm {{ request('sort') == 'rating' ? 'active' : '' }}">Nổi
                    bật</a>

                <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'newest'])) }}"
                    class="btn btn-outline-secondary btn-sm {{ (!request('sort') || request('sort') == 'newest') ? 'active' : '' }}">Mới
                    nhất</a>

                <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'price_asc'])) }}"
                    class="btn btn-outline-secondary btn-sm {{ request('sort') == 'price_asc' ? 'active' : '' }}">Giá
                    ↑</a>

                <a href="{{ route('restaurants.index', array_merge($currentParams, ['sort' => 'price_desc'])) }}"
                    class="btn btn-outline-secondary btn-sm {{ request('sort') == 'price_desc' ? 'active' : '' }}">Giá
                    ↓</a>
            </div>
        </div>

        <!-- GRID DANH SÁCH NHÀ HÀNG -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse($restaurants as $restaurant)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all position-relative">

                    <!-- Ảnh tỉ lệ 4:3 bao phủ -->
                    <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;">
                        <img src="{{ $restaurant->image ?? 'https://placehold.co/600x450?text=No+Image' }}"
                            class="w-100 h-100 object-fit-cover" alt="{{ $restaurant->name }}">

                        <!-- Badge danh mục đè lên góc trên bên trái ảnh -->
                        <span
                            class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 backdrop-blur py-2 px-3 fs-7">
                            {{ $restaurant->category->name }}
                        </span>
                    </div>

                    <!-- Nội dung Card -->
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold mb-2">
                            <a href="{{ url('/restaurants/' . $restaurant->id) }}"
                                class="text-decoration-none text-dark link-primary">
                                {{ $restaurant->name }}
                            </a>
                        </h5>

                        <!-- Địa chỉ (Dùng icon unicode định vị thay thế nếu không cài sẵn icon pack) -->
                        <p class="card-text text-muted small mb-3 text-truncate">
                            📍 {{ $restaurant->address }}, {{ $restaurant->district }}, {{ $restaurant->city }}
                        </p>

                        <!-- Đánh giá và Giờ mở cửa -->
                        <div
                            class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                            <div>
                                <span class="text-warning fw-bold">★</span>
                                <span
                                    class="fw-bold text-dark">{{ round($restaurant->average_rating, 1) ?? '0.0' }}</span>
                            </div>
                            <div class="small text-muted">
                                🕒 {{ \Carbon\Carbon::parse($restaurant->open_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($restaurant->close_time)->format('H:i') }}
                            </div>
                        </div>
                    </div>

                    <!-- Badge mức giá nằm dưới cùng góc phải card để gọn gàng -->
                    <div class="position-absolute bottom-0 end-0 m-4 mb-5 pb-2">
                        @php
                        // Chuyển đổi mức giá sang định dạng $ / $$ / $$$ tùy thuộc vào cấu trúc dữ liệu của bạn
                        // Ở đây giả định: level 1 (<300k), level 2 (300k-1tr), level 3 (>1tr) hoặc lấy thẳng số trường
                            mức giá nếu lưu dạng 1, 2, 3
                            $priceBadge = '$';
                            if($restaurant->price_range > 1000000) $priceBadge = '$$$';
                            elseif($restaurant->price_range > 300000) $priceBadge = '$$';
                            @endphp
                            <span
                                class="badge bg-light text-success border border-success border-opacity-25">{{ $priceBadge }}</span>
                    </div>

                </div>
            </div>
            @empty
            <!-- EMPTY STATE -->
            <div class="col-12 py-5 text-center">
                <div class="mb-3 fs-1 text-muted">🍽️</div>
                <h4 class="fw-bold text-secondary">Không tìm thấy kết quả phù hợp</h4>
                <p class="text-muted">Bạn hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm xem sao nhé.</p>
                <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-sm mt-2">Đặt lại bộ lọc</a>
            </div>
            @endforelse
        </div>

        <!-- PHÂN TRANG BOOTSTRAP 5 -->
        <div class="d-flex justify-content-center mt-5">
            {{ $restaurants->withQueryString()->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <!-- Thêm hiệu ứng hover nhẹ nhàng bằng CSS inline cho gọn -->
    <style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .backdrop-blur {
        backdrop-filter: blur(4px);
    }
    </style>
    @endsection
    @extends('layouts.admin')

    @section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Quản lý Nhà hàng</h2>
            <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
                + Thêm nhà hàng
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Bộ lọc -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.restaurants.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="city" class="form-control" placeholder="Tìm theo thành phố..."
                            value="{{ request('city') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary w-100">Lọc Dữ Liệu</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng dữ liệu -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tên nhà hàng</th>
                                <th>Danh mục</th>
                                <th>Thành phố</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($restaurants as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="Ảnh" class="rounded"
                                        style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                    <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $item->name }}</td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>{{ $item->city }}</td>
                                <td>
                                    @if($item->status == 1)
                                    <span class="badge bg-success">Hoạt động</span>
                                    @else
                                    <span class="badge bg-danger">Ngừng HĐ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.restaurants.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    @if($item->status == 1)
                                    <form action="{{ route('admin.restaurants.destroy', $item->id) }}" method="POST"
                                        class="d-inline-block"
                                        onsubmit="return confirm('Bạn có chắc muốn vô hiệu hóa nhà hàng này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Tắt</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Không tìm thấy nhà hàng nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $restaurants->withQueryString()->links() }}
            </div>
        </div>
    </div>
    @endsection
</body>

</html>