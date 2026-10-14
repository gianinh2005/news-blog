<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Tin tức & Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; }
        .sidebar .nav-link { color: #adb5bd; border-radius: 8px; margin-bottom: 4px; padding: 10px 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: #343a40; }
        .stat-card { border: none; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="d-flex align-items-center mb-4 px-2">
                    <i class="bi bi-shield-check text-warning fs-4 me-2"></i>
                    <span class="fs-5 fw-bold text-white">ADMIN PANEL</span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Báo cáo tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                            <i class="bi bi-file-earmark-richtext me-2"></i> Quản lý Bài viết
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-tags me-2"></i> Quản lý Danh mục
                        </a>
                    </li>
                    <li class="nav-item border-top border-secondary pt-3 mt-4">
                        <a class="nav-link text-info" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Xem trang Client
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
