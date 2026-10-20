<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tin tức & Blog cá nhân')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        .post-card { transition: transform 0.2s, box-shadow 0.2s; border: none; border-radius: 12px; overflow: hidden; }
        .post-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
        .badge-type { position: absolute; top: 12px; left: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ route('home') }}"><i class="bi bi-newspaper"></i> NEWS & BLOG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') && !request('type') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link {{ request('type') == 'news' ? 'active' : '' }}" href="{{ route('home', ['type' => 'news']) }}">Tin tức</a></li>
                    <li class="nav-item"><a class="nav-link {{ request('type') == 'blog' ? 'active' : '' }}" href="{{ route('home', ['type' => 'blog']) }}">Góc Blog</a></li>
                </ul>

                <form class="d-flex me-3" action="{{ route('home') }}" method="GET">
                    <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Tìm kiếm bài viết..." value="{{ request('q') }}">
                    <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <ul class="navbar-nav align-items-center">
                    @auth
                        <li class="nav-item me-2">
                            <a class="btn btn-warning btn-sm text-dark fw-semibold" href="{{ route('user.posts.create') }}">
                                <i class="bi bi-pencil-square me-1"></i> Viết bài
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link text-warning fw-semibold" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock"></i> Quản trị</a></li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.posts.index') }}">
                                        <i class="bi bi-journal-text me-2 text-primary"></i> Blog của tôi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">Đăng ký</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4 min-vh-100">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 text-center text-muted">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} Tin Tức & Blog Cá Nhân - Xây dựng với Laravel & MySQL</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Stack cho các script tùy chỉnh của từng trang (vd: counter ký tự bình luận) --}}
    @stack('scripts')
</body>
</html>
