<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <title>TableGo - Nền tảng đặt bàn nhà hàng thông minh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        .text-danger-pasgo {
            color: #dc3545 !important;
        }

        .bg-danger-pasgo {
            background-color: #dc3545 !important;
        }

        .footer-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
            font-size: 0.9rem;
            line-height: 1.8;
            display: block;
        }

        .footer-link:hover {
            color: #dc3545;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger-pasgo fs-4 fst-italic" href="/">
                <i class="bi bi-shop"></i> TableGo
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold ms-4">
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/restaurants">Danh sách nhà hàng</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#collections">Bộ sưu tập</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#blog">Tin tức & Blog</a></li>
                </ul>

                <div class="d-flex align-items-center gap-4">
                    <div class="fw-bold text-danger-pasgo d-none d-lg-block">
                        <i class="bi bi-telephone-fill me-1"></i> 1900.xxxx.xxx
                    </div>

                    @guest
                    <a href="/login" class="btn btn-outline-dark rounded-pill px-4 fw-semibold small">Đăng nhập</a>
                    <a href="/register" class="btn bg-danger-pasgo text-white rounded-pill px-4 fw-semibold small">Đăng ký</a>
                    @else
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle rounded-pill fw-semibold border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1 text-secondary"></i> Xin chào, {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            @if(Auth::user()->role === 'admin')
                            <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Trang quản trị</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-calendar-check me-2"></i>Lịch sử đặt bàn</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white border-top pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-dark">Đặt chỗ & Ưu đãi</h5>
                    <p class="text-muted small lh-lg mb-0">TableGo là nền tảng đặt chỗ trực tuyến, giúp thực khách tìm kiếm và lựa chọn nhà hàng đúng ý gần nhất. Với các đối tác nhà hàng sẽ dễ dàng và hiệu quả hơn để tăng doanh số, hiệu suất bán hàng!</p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3 text-dark">Giới thiệu</h5>
                    <a href="#" class="footer-link">Tổng quan về nền tảng</a>
                    <a href="#" class="footer-link">Hướng dẫn đặt chỗ</a>
                    <a href="#" class="footer-link">Câu hỏi thường gặp</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3 text-dark">Tiện ích</h5>
                    <a href="#" class="footer-link">Địa điểm gần bạn</a>
                    <a href="#" class="footer-link">Ưu đãi đang Hot</a>
                    <a href="#" class="footer-link">Khám phá Bộ sưu tập</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3 text-dark">Chính sách</h5>
                    <a href="#" class="footer-link">Điều khoản sử dụng</a>
                    <a href="#" class="footer-link">Chính sách bảo mật</a>
                    <a href="#" class="footer-link">Quy chế hoạt động</a>
                </div>
                <div class="col-lg-3 col-md-12">
                    <h5 class="fw-bold mb-3 text-dark">Dành cho Kinh doanh</h5>
                    <a href="#" class="footer-link">Trung tâm hỗ trợ Đối tác</a>
                    <a href="#" class="footer-link">Hướng dẫn nhà hàng hợp tác</a>
                    <a href="#" class="footer-link">Đăng ký hợp tác ngay</a>
                </div>
            </div>

            <div class="row pt-4 border-top">
                <div class="col-lg-8">
                    <h6 class="fw-bold text-dark text-uppercase mb-3">Thông tin doanh nghiệp</h6>
                    <p class="text-muted small mb-1"><strong>Địa chỉ:</strong> Đại học Phenikaa</p>
                    <p class="text-muted small mb-1"><strong>Hotline:</strong> 1900.xxxx.xxx | <strong>Email:</strong> cskh@TableGo.vn</p>
                    <p class="text-muted small mb-3"><strong>Mã số thuế:</strong> 0123456789</p>
                    <p class="text-muted small mb-3">© Copyright 2026 TableGo. All rights reserved.</p>
                    <button class="btn btn-outline-danger btn-sm rounded-pill fw-semibold px-4"><i class="bi bi-send me-1"></i> Gửi góp ý</button>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <p class="fw-bold text-dark mb-2">Chứng nhận bởi</p>
                    <img src="https://pasgo.vn/Content/Images/dathongbao-bct.png" alt="Bộ công thương" height="40" class="me-2">
                    <img src="https://images.dmca.com/Badges/dmca_protected_sml_120n.png?ID=2" alt="DMCA" height="25">
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>