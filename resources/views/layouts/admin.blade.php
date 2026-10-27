<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - NEWS & BLOG</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: #0f172a;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 1.5rem 1rem;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 2rem;
            text-decoration: none;
        }

        .admin-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .admin-nav-item .nav-link {
            color: #94a3b8;
            border-radius: 10px;
            margin-bottom: 0.35rem;
            padding: 0.7rem 1rem;
            font-weight: 500;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .admin-nav-item .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }

        .admin-nav-item .nav-link.active {
            color: #ffffff;
            background: #2563eb;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .admin-nav-item .nav-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        .admin-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px;
            margin-bottom: 1.75rem;
        }

        .stat-card-gradient,
        .stat-card-solid {
            border: none;
            border-radius: 16px;
            color: #ffffff;
            padding: 1.5rem;
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease;
        }

        .stat-card-gradient:hover,
        .stat-card-solid:hover {
            transform: translateY(-4px);
        }

        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Custom Pagination Styling for Clean Look */
        .pagination {
            margin-bottom: 0 !important;
            gap: 4px;
        }
        .pagination .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        }
        .pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
        }
        /* Khống chế kích thước mọi SVG phân trang để không bị to đột biến */
        .pagination svg,
        nav[role="navigation"] svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                    <div class="admin-brand-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white fs-6">ADMIN PORTAL</div>
                        <div class="text-white-50" style="font-size: 0.72rem;">Hệ thống Quản trị</div>
                    </div>
                </a>

                <ul class="nav flex-column admin-nav-item">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-grid-1x2-fill me-2"></i> Báo cáo tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                            <i class="bi bi-file-earmark-richtext-fill me-2"></i> Quản lý Bài viết
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-tags-fill me-2"></i> Quản lý Danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
                            <i class="bi bi-chat-left-text-fill me-2"></i> Quản lý Bình luận
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people-fill me-2"></i> Quản lý Tài khoản
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                                href="{{ route('admin.reports.index') }}">
                            <i class="bi bi-flag-fill me-2"></i> Xử lý Báo cáo
                            @php
                                $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
                            @endphp
                            @if($pendingReportsCount > 0)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingReportsCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}"
                                href="{{ route('admin.subscribers.index') }}">
                            <i class="bi bi-envelope-paper-fill me-2"></i> Bản tin Email
                        </a>
                    </li>

                    <li class="nav-item border-top border-secondary pt-3 mt-4">
                        <a class="nav-link text-info" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Xem trang Client
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-10 p-3 p-md-4">
                {{-- Header bar --}}
                <div class="admin-header shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Xin chào,</span>
                        <span class="fw-bold text-dark">{{ auth()->user()->name }}</span>
                        <span class="badge bg-warning text-dark small rounded-pill px-2">Quản trị viên</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('user.posts.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Soạn bài viết
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" type="submit">
                                <i class="bi bi-box-arrow-right me-1"></i> Thoát
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
