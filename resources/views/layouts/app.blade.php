<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tin tức & Blog cá nhân')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Modern Editorial Design System -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>
    <!-- Top Reading Progress Indicator -->
    <div id="reading-progress"></div>

    <!-- Glassmorphic Header & Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <!-- Brand Logo with solid badge -->
            <a class="brand-logo me-4" href="{{ route('home') }}">
                <div class="brand-icon">
                    <i class="bi bi-newspaper"></i>
                </div>
                <span class="brand-text">NEWS & BLOG</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <!-- Navigation Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home') && !request('type') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request('type') == 'news' ? 'active' : '' }}" href="{{ route('home', ['type' => 'news']) }}">
                            <i class="bi bi-lightning-charge me-1"></i> Tin tức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request('type') == 'blog' ? 'active' : '' }}" href="{{ route('home', ['type' => 'blog']) }}">
                            <i class="bi bi-feather me-1"></i> Góc Blog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('members.index') ? 'active' : '' }}" href="{{ route('members.index') }}">
                            <i class="bi bi-trophy-fill me-1 text-warning"></i> Thành viên
                        </a>
                    </li>
                </ul>

                <!-- Pill Search Form -->
                <form class="d-flex me-3" action="{{ route('home') }}" method="GET">
                    <div class="nav-search-wrap">
                        <i class="bi bi-search nav-search-icon"></i>
                        <input class="nav-search-input" type="search" name="q" placeholder="Tìm kiếm bài viết..." value="{{ request('q') }}">
                    </div>
                </form>

                <!-- Auth & Action Buttons -->
                <ul class="navbar-nav align-items-center gap-2">
                    @auth
                        <li class="nav-item">
                            <a class="btn-write-post text-decoration-none" href="{{ route('user.posts.create') }}">
                                <i class="bi bi-pencil-square"></i>
                                <span>Viết bài</span>
                            </a>
                        </li>

                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom text-warning" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-check me-1"></i> Quản trị
                                </a>
                            </li>
                        @endif

                        <!-- Notification Bell Dropdown -->
                        @php
                            $unreadNotifsCount = auth()->user()->unreadNotifications()->count();
                            $recentNotifs = auth()->user()->notifications()->take(6)->get();
                        @endphp
                        <li class="nav-item dropdown me-1">
                            <a class="nav-link nav-link-custom position-relative px-2 d-flex align-items-center" href="#" data-bs-toggle="dropdown" title="Thông báo">
                                <i class="bi bi-bell-fill fs-5"></i>
                                @if($unreadNotifsCount > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" id="notifBadge" style="font-size: 0.65rem;">
                                        {{ $unreadNotifsCount > 9 ? '9+' : $unreadNotifsCount }}
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-0" style="width: 320px; max-width: 90vw;">
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light rounded-top-4">
                                    <span class="fw-bold small text-dark"><i class="bi bi-bell-fill text-primary me-1"></i> Thông báo</span>
                                    @if($unreadNotifsCount > 0)
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none text-primary small fw-semibold" id="markAllReadBtn" style="font-size: 0.75rem;">
                                            Đã đọc tất cả
                                        </button>
                                    @endif
                                </div>
                                <div class="list-group list-group-flush" style="max-height: 320px; overflow-y: auto;">
                                    @forelse($recentNotifs as $notif)
                                        <a href="{{ $notif->data['url'] ?? '#' }}" class="list-group-item list-group-item-action p-2 px-3 border-bottom {{ is_null($notif->read_at) ? 'bg-primary-subtle' : '' }}" onclick="markNotifRead('{{ $notif->id }}')">
                                            <div class="d-flex gap-2 align-items-start">
                                                <div class="flex-shrink-0 mt-1">
                                                    <div class="avatar-badge" style="width: 28px; height: 28px; font-size: 0.72rem; background: #2563eb;">
                                                        @if(($notif->data['type'] ?? '') === 'reaction')
                                                            ❤️
                                                        @elseif(($notif->data['type'] ?? '') === 'follow')
                                                            👥
                                                        @elseif(($notif->data['type'] ?? '') === 'post_approved')
                                                            🎉
                                                        @elseif(($notif->data['type'] ?? '') === 'post_rejected')
                                                            ⚠️
                                                        @else
                                                            💬
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="fw-semibold text-dark small text-truncate" style="font-size: 0.8rem;">{{ $notif->data['title'] ?? 'Thông báo mới' }}</div>
                                                    <div class="text-muted small text-truncate" style="font-size: 0.75rem;">{{ $notif->data['message'] ?? '' }}</div>
                                                    <div class="text-secondary small mt-1" style="font-size: 0.68rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-4 text-center text-muted small">
                                            <i class="bi bi-bell-slash fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                            Bạn chưa có thông báo nào.
                                        </div>
                                    @endforelse
                                </div>
                                <div class="p-2 text-center border-top bg-light rounded-bottom-4">
                                    <a href="{{ route('notifications.index') }}" class="small fw-semibold text-decoration-none text-primary">Xem tất cả thông báo</a>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="user-pill-btn dropdown-toggle text-decoration-none d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        @if (auth()->user()->avatar)
                        <img
                            src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            alt="Ảnh đại diện"
                            class="avatar-badge"
                            style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;"
                        >
                        @else
                            <span class="avatar-badge">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        @endif
                                <span>{{ Str::limit(auth()->user()->name, 14) }}</span>
                                <span class="badge rounded-pill text-white fw-bold shadow-sm" style="background: {{ auth()->user()->level_badge_color }}; font-size: 0.68rem; padding: 2px 7px;">Lv.{{ auth()->user()->level }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 py-2" style="min-width: 230px;">
                                <li class="px-3 py-2 border-bottom mb-1 bg-light rounded-top-4">
                                    <div class="small fw-bold text-dark">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted text-truncate" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                                    <div class="mt-2 pt-1 d-flex align-items-center justify-content-between border-top">
                                        <span class="badge rounded-pill text-white shadow-sm" style="background: {{ auth()->user()->level_badge_color }}; font-size: 0.7rem; padding: 3px 8px;">
                                            Lv.{{ auth()->user()->level }} {{ auth()->user()->level_title }}
                                        </span>
                                        <span class="small fw-semibold text-primary" style="font-size: 0.75rem;">
                                            <i class="bi bi-stars"></i> {{ number_format(auth()->user()->exp) }} EXP
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('members.index') }}">
                                        <i class="bi bi-trophy-fill me-2 text-warning"></i> Bảng xếp hạng thành viên
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('user.posts.index') }}">
                                        <i class="bi bi-journal-richtext me-2 text-primary"></i> Blog của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit', ['tab' => 'saved']) }}">
                                        <i class="bi bi-bookmark-heart-fill me-2 text-danger"></i> Bài viết đã lưu
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('notifications.index') }}">
                                        <i class="bi bi-bell me-2 text-warning"></i> Trung tâm thông báo
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        Thông tin cá nhân
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item py-2 text-danger" type="submit">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="{{ route('login') }}">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-write-post text-decoration-none" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="py-4 min-vh-100">
        <div class="container">
            {{-- Flash Alert --}}
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
    </main>

    <!-- Sleek Editorial Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="brand-logo mb-3">
                        <div class="brand-icon">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <span class="brand-text">NEWS & BLOG</span>
                    </div>
                    <p class="pe-lg-4" style="color: #cbd5e1; line-height: 1.7; font-size: 0.92rem;">
                        Nền tảng xuất bản tin tức hiện đại và chia sẻ blog cá nhân. Cập nhật những dòng chảy thời sự, công nghệ, tri thức và câu chuyện cuộc sống đa chiều mỗi ngày.
                    </p>
                    <div class="d-flex align-items-center mt-3">
                        <a href="#" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="footer-social-btn" title="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="footer-social-btn" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" class="footer-social-btn" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <h6>CHUYÊN MỤC</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ route('home', ['type' => 'news']) }}"><i class="bi bi-chevron-right me-1 small"></i> Tin tức thời sự</a></li>
                        <li><a href="{{ route('home', ['type' => 'blog']) }}"><i class="bi bi-chevron-right me-1 small"></i> Blog cá nhân</a></li>
                        <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-1 small"></i> Công nghệ & Lập trình</a></li>
                        <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-1 small"></i> Đọc sách & Kỹ năng</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <h6>BẢN TIN BÀI VIẾT</h6>
                    <p class="small mb-3" style="color: #cbd5e1;">Nhận các bài viết mới và chất lượng nhất được gửi định kỳ hàng tuần.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" required class="form-control form-control-sm rounded-start-pill border-0 px-3" placeholder="Nhập email của bạn...">
                            <button class="btn btn-primary btn-sm rounded-end-pill px-3 fw-semibold" type="submit">Đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="border-top pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center small" style="border-color: #334155 !important; color: #cbd5e1;">
                <div>© {{ date('Y') }} NEWS & BLOG. Tất cả quyền được bảo lưu.</div>
                <div class="mt-2 mt-md-0 d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#accessibilityModal">
                        <i class="bi bi-eye-fill me-1 text-primary"></i> Chế độ hỗ trợ thị lực
                    </button>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Phiên bản Laravel 13</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- NÚT FLOATING HỖ TRỢ THỊ GIÁC / NGƯỜI KHIẾM THỊ GÓC MÀN HÌNH --}}
    <button type="button" 
            class="btn btn-warning rounded-circle accessibility-floating-btn d-flex align-items-center justify-content-center border-2 border-white shadow"
            style="width: 50px; height: 50px; font-size: 1.35rem;" 
            data-bs-toggle="modal" 
            data-bs-target="#accessibilityModal" 
            title="Tùy chọn giao diện hỗ trợ thị giác / người lớn tuổi">
        <i class="bi bi-universal-access text-dark"></i>
    </button>

    {{-- MODAL CÀI ĐẶT HỖ TRỢ THỊ LỰC (ACCESSIBILITY SETTINGS) --}}
    <div class="modal fade" id="accessibilityModal" tabindex="-1" aria-labelledby="accessibilityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-3 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="accessibilityModalLabel">
                        <i class="bi bi-universal-access fs-4 text-warning"></i> Hỗ trợ Thị giác & Dễ đọc
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">
                        Tùy chỉnh kích thước chữ và màu sắc màn hình phù hợp cho người cao tuổi, người thị lực kém hoặc mỏi mắt. Cài đặt sẽ được tự động ghi nhớ trên máy của bạn.
                    </p>

                    {{-- Cỡ chữ --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-type me-1 text-primary"></i> Kích thước chữ (Font Size)</span>
                            <span id="current-font-label" class="badge bg-primary-subtle text-primary">Mặc định</span>
                        </label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary py-2" onclick="setFontSize('normal')">
                                Vừa <small>(Mặc định)</small>
                            </button>
                            <button type="button" class="btn btn-outline-secondary py-2 fw-semibold" onclick="setFontSize('large')">
                                Lớn <small>(+25%)</small>
                            </button>
                            <button type="button" class="btn btn-outline-secondary py-2 fw-bold" onclick="setFontSize('xlarge')">
                                Rất Lớn <small>(+50%)</small>
                            </button>
                        </div>
                    </div>

                    {{-- Chủ đề màu sắc / Tương phản --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <i class="bi bi-palette me-1 text-primary"></i> Độ tương phản & Tông màu bảo vệ mắt
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-dark w-100 py-2 text-start d-flex align-items-center gap-2 rounded-3" onclick="setTheme('default')">
                                    <span class="rounded-circle border" style="width: 18px; height: 18px; background: #ffffff;"></span>
                                    <span>Giao diện chuẩn</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-dark w-100 py-2 text-start d-flex align-items-center gap-2 rounded-3" onclick="setTheme('sepia')">
                                    <span class="rounded-circle border" style="width: 18px; height: 18px; background: #fbf0d9;"></span>
                                    <span>Vàng dịu (Ấm mắt)</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-dark w-100 py-2 text-start d-flex align-items-center gap-2 rounded-3" onclick="setTheme('dark')">
                                    <span class="rounded-circle" style="width: 18px; height: 18px; background: #0f172a;"></span>
                                    <span>Chế độ tối (Dark)</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-dark w-100 py-2 text-start d-flex align-items-center gap-2 rounded-3 bg-black text-warning border-warning" onclick="setTheme('high-contrast')">
                                    <span class="rounded-circle" style="width: 18px; height: 18px; background: #ffff00;"></span>
                                    <span class="fw-bold">Tương phản cao</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Phông chữ dễ đọc cho người khó đọc --}}
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="dyslexicToggle" onchange="toggleDyslexicFont(this.checked)">
                        <label class="form-check-label fw-semibold text-dark small" for="dyslexicToggle">
                            Bật phông chữ giãn cách rộng (Dễ đọc / Chống hoa mắt)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-auto" onclick="resetAccessibility()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục mặc định
                    </button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Reading Progress Bar Script & Accessibility Engine -->
    <script>
        // Reading Progress
        window.addEventListener('scroll', () => {
            const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            const progressEl = document.getElementById('reading-progress');
            if (progressEl) {
                progressEl.style.width = scrolled + '%';
            }
        });

        // Accessibility Functions (Cỡ chữ, Màu tương phản, Phông chữ)
        function applyAccessibilitySettings() {
            const savedSize = localStorage.getItem('nb_font_size') || 'normal';
            const savedTheme = localStorage.getItem('nb_theme') || 'default';
            const savedDyslexic = localStorage.getItem('nb_dyslexic') === 'true';

            // Apply Font Size
            document.body.classList.remove('font-size-large', 'font-size-xlarge');
            const fontLabel = document.getElementById('current-font-label');
            if (savedSize === 'large') {
                document.body.classList.add('font-size-large');
                if (fontLabel) fontLabel.textContent = 'Lớn (+25%)';
            } else if (savedSize === 'xlarge') {
                document.body.classList.add('font-size-xlarge');
                if (fontLabel) fontLabel.textContent = 'Rất Lớn (+50%)';
            } else {
                if (fontLabel) fontLabel.textContent = 'Vừa (Mặc định)';
            }

            // Apply Theme
            document.body.classList.remove('theme-sepia', 'theme-dark', 'theme-high-contrast');
            if (savedTheme === 'sepia') {
                document.body.classList.add('theme-sepia');
            } else if (savedTheme === 'dark') {
                document.body.classList.add('theme-dark');
            } else if (savedTheme === 'high-contrast') {
                document.body.classList.add('theme-high-contrast');
            }

            // Apply Dyslexic Font
            const toggle = document.getElementById('dyslexicToggle');
            if (savedDyslexic) {
                document.body.classList.add('font-dyslexic');
                if (toggle) toggle.checked = true;
            } else {
                document.body.classList.remove('font-dyslexic');
                if (toggle) toggle.checked = false;
            }
        }

        function setFontSize(size) {
            localStorage.setItem('nb_font_size', size);
            applyAccessibilitySettings();
        }

        function setTheme(theme) {
            localStorage.setItem('nb_theme', theme);
            applyAccessibilitySettings();
        }

        function toggleDyslexicFont(enabled) {
            localStorage.setItem('nb_dyslexic', enabled ? 'true' : 'false');
            applyAccessibilitySettings();
        }

        function resetAccessibility() {
            localStorage.removeItem('nb_font_size');
            localStorage.removeItem('nb_theme');
            localStorage.removeItem('nb_dyslexic');
            applyAccessibilitySettings();
        }

        // Tự động nạp cấu hình khi mở trang
        document.addEventListener('DOMContentLoaded', applyAccessibilitySettings);
        applyAccessibilitySettings();

        // Xử lý thông báo đã đọc
        function markNotifRead(id) {
            fetch(`/thong-bao/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).catch(console.error);
        }

        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('/thong-bao/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    const badge = document.getElementById('notifBadge');
                    if (badge) badge.remove();
                    markAllBtn.remove();
                    document.querySelectorAll('.notif-item').forEach(el => el.classList.remove('bg-primary-subtle'));
                }).catch(console.error);
            });
        }

        // Xử lý Bookmark nhanh
        function toggleBookmark(postId, btnEl) {
            @guest
                window.location.href = '{{ route("login") }}';
                return;
            @endguest

            fetch(`/bai-viet/${postId}/bookmark`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = btnEl.querySelector('i');
                    const textSpan = btnEl.querySelector('.bookmark-text');
                    if (data.is_saved) {
                        btnEl.classList.add('active', 'btn-warning');
                        btnEl.classList.remove('btn-outline-warning', 'btn-outline-secondary');
                        if (icon) {
                            icon.classList.remove('bi-bookmark');
                            icon.classList.add('bi-bookmark-fill');
                        }
                        if (textSpan) textSpan.textContent = 'Đã lưu';
                        btnEl.setAttribute('title', 'Bỏ lưu bài viết');
                    } else {
                        btnEl.classList.remove('active', 'btn-warning');
                        btnEl.classList.add('btn-outline-warning');
                        if (icon) {
                            icon.classList.remove('bi-bookmark-fill');
                            icon.classList.add('bi-bookmark');
                        }
                        if (textSpan) textSpan.textContent = 'Lưu bài';
                        btnEl.setAttribute('title', 'Lưu bài viết xem sau');
                    }
                }
            })
            .catch(console.error);
        }

        // Xử lý Theo dõi tác giả nhanh
        function toggleFollow(authorId, btnEl) {
            @guest
                window.location.href = '{{ route("login") }}';
                return;
            @endguest

            fetch(`/tac-gia/${authorId}/follow`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = btnEl.querySelector('i');
                    const textSpan = btnEl.querySelector('.follow-text');
                    if (data.is_following) {
                        btnEl.classList.add('active', 'btn-primary');
                        btnEl.classList.remove('btn-outline-primary');
                        if (icon) {
                            icon.classList.remove('bi-person-plus');
                            icon.classList.add('bi-check-lg');
                        }
                        if (textSpan) textSpan.textContent = 'Đang theo dõi';
                    } else {
                        btnEl.classList.remove('active', 'btn-primary');
                        btnEl.classList.add('btn-outline-primary');
                        if (icon) {
                            icon.classList.remove('bi-check-lg');
                            icon.classList.add('bi-person-plus');
                        }
                        if (textSpan) textSpan.textContent = 'Theo dõi';
                    }

                    // Cập nhật số follower nếu có thẻ hiển thị
                    const countEl = document.getElementById(`author-followers-count-${authorId}`);
                    if (countEl && data.followers_count !== undefined) {
                        countEl.textContent = data.followers_count;
                    }
                } else if (data.message) {
                    alert(data.message);
                }
            })
            .catch(console.error);
        }
    </script>

    {{-- Stack cho script tùy chỉnh của trang con --}}
    @stack('scripts')
</body>
</html>
