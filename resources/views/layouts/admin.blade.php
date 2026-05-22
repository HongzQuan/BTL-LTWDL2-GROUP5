<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hệ thống đặt bàn</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            width: 250px;
            background-color: #343a40;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background-color: #495057;
        }

        .content-wrapper {
            flex: 1;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar flex-shrink-0 d-flex flex-column">
        <h4 class="text-white text-center py-3 border-bottom border-secondary m-0">Admin Panel</h4>
        <div class="mt-3 flex-grow-1">
            <a href="{{ url('/admin') }}" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <!-- Thêm mới nút Danh mục -->
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags me-2"></i> Danh mục
            </a>

            <!-- Trả lại nút Nhà hàng về đúng vị trí (tạm thời để dấu #) -->
            <a href="{{ route('restaurants.index') }}">
                <i class="bi bi-shop me-2"></i> Nhà hàng
            </a>
            <a href="#"><i class="bi bi-ui-radios-grid me-2"></i> Bàn ăn</a>
            <a href="#"><i class="bi bi-menu-button-wide me-2"></i> Thực đơn</a>
            <a href="{{ route('bookings.index') }}"><i class="bi bi-calendar-check me-2"></i> Đặt bàn</a>
            <a href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i> Khách hàng</a>
            <a href="#"><i class="bi bi-star me-2"></i> Đánh giá</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper d-flex flex-column">
        <!-- Topbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none"><i class="bi bi-list"></i></button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 fw-medium">Xin chào, {{ Auth::user()->name ?? 'Admin' }}</span>
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>