@extends('layouts.app')

@section('title', 'Bảng Xếp Hạng Thành Viên & Cộng Đồng Tác Giả')

@section('content')
<div class="container py-4">

    <!-- Header Banner -->
    <div class="card border rounded-4 overflow-hidden mb-4 shadow-sm" style="background: #0f172a; border-color: #1e293b !important;">
        <div class="card-body p-4 p-lg-5 text-white position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                        <i class="bi bi-trophy-fill me-1"></i> BẢNG VINH DANH TÁC GIẢ
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">Cộng Đồng Tác Giả & Bảng Xếp Hạng</h1>
                    <p class="mb-4 text-light" style="max-width: 650px; font-size: 1.05rem; opacity: 0.92;">
                        Khám phá những cây bút tích cực nhất và những tác giả sở hữu bài viết có lượt tương tác bùng nổ nhất trên nền tảng.
                    </p>

                    <!-- Stats Quick Bar -->
                    <div class="d-flex flex-wrap gap-4 text-white">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-people-fill text-warning fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold leading-none text-white">{{ number_format($stats['total_members']) }}</div>
                                <div class="small text-light" style="opacity: 0.85;">Thành viên</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-journal-text text-info fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold leading-none text-white">{{ number_format($stats['total_posts']) }}</div>
                                <div class="small text-light" style="opacity: 0.85;">Bài xuất bản</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart-fill text-danger fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold leading-none text-white">{{ number_format($stats['total_reactions']) }}</div>
                                <div class="small text-light" style="opacity: 0.85;">Lượt thả cảm xúc</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-eye-fill text-success fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold leading-none text-white">{{ number_format($stats['total_views']) }}</div>
                                <div class="small text-light" style="opacity: 0.85;">Lượt đọc</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 d-none d-lg-block">
                    <div class="p-3 bg-white bg-opacity-10 backdrop-blur rounded-4 text-start border border-white border-opacity-10 shadow">
                        <div class="d-flex align-items-center gap-2 text-warning fw-bold mb-2 small">
                            <i class="bi bi-award-fill"></i> QUY TẮC TÍCH ĐIỂM & LEVEL
                        </div>
                        <ul class="list-unstyled small text-light mb-0 ps-1" style="opacity: 0.92;">
                            <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> Xuất bản bài viết: <strong>+100 EXP / bài</strong></li>
                            <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> Đăng càng nhiều bài, Level càng cao</li>
                            <li class="mb-0"><i class="bi bi-check2 text-info me-1"></i> Lượt tương tác tính riêng: Views + Icon + Comments</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs between 2 Leaderboards -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <ul class="nav nav-pills gap-2 p-1 bg-white rounded-pill shadow-sm border" id="leaderboardTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'posts' ? 'active' : '' }}" 
                   href="{{ route('members.index', ['tab' => 'posts', 'q' => $search]) }}"
                   style="{{ $activeTab === 'posts' ? 'background: #2563eb; color: #fff;' : 'color: #475569;' }}">
                    <i class="bi bi-pen-fill me-2"></i> 1. Top Đăng Bài & Điểm Cấp Độ (EXP)
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'interactions' ? 'active' : '' }}" 
                   href="{{ route('members.index', ['tab' => 'interactions', 'q' => $search]) }}"
                   style="{{ $activeTab === 'interactions' ? 'background: #e11d48; color: #fff;' : 'color: #475569;' }}">
                    <i class="bi bi-fire me-2"></i> 2. Top Lượt Tương Tác Cao Nhất
                </a>
            </li>
        </ul>

        <!-- Member Search Box -->
        <form action="{{ route('members.index') }}" method="GET" class="d-flex gap-2">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="input-group shadow-sm">
                <input type="text" name="q" class="form-control rounded-start-pill ps-3 border-end-0" placeholder="Tìm thành viên theo tên..." value="{{ $search }}">
                <button class="btn btn-outline-secondary rounded-end-pill px-3 border-start-0" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            @if(!empty($search))
                <a href="{{ route('members.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center" title="Xóa tìm kiếm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>

    @if($activeTab === 'posts')
        <!-- ========================================== -->
        <!-- MỤC 1: XẾP HẠNG ĐĂNG BÀI & ĐIỂM EXP/LEVEL -->
        <!-- ========================================== -->
        <div class="alert alert-primary bg-primary-subtle border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </div>
            <div class="small text-dark">
                <div><strong>Tiêu chí xếp hạng mục này:</strong> Thưởng điểm kinh nghiệm (EXP) dựa trên mức độ chăm chỉ đóng góp. Mỗi bài viết được ban quản trị kiểm duyệt và xuất bản sẽ thưởng ngay <strong>+100 EXP</strong> giúp nâng cấp Level và nhận danh hiệu tác giả cao quý!</div>
                <div class="mt-1 text-primary-emphasis" style="font-size: 0.78rem;"><i class="bi bi-shield-x me-1"></i><em>Bảng xếp hạng chỉ dành riêng cho các thành viên cộng đồng & tác giả, không áp dụng cho tài khoản Quản trị viên (Admin).</em></div>
            </div>
        </div>

        <!-- Top 3 Podium for Posters -->
        @if($topPosters->count() >= 3 && empty($search))
        <div class="row g-3 mb-4 justify-content-center align-items-end">
            <!-- Top 2 -->
            @php $top2 = $topPosters[1] ?? null; @endphp
            @if($top2)
            <div class="col-md-4 col-sm-6 order-2 order-md-1">
                <div class="card border-0 rounded-4 shadow-sm text-center p-3 h-100 bg-white" style="border-top: 4px solid #94a3b8 !important;">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        @if ($top2->avatar)
                            <img src="{{ asset('storage/' . $top2->avatar) }}" class="rounded-circle shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">
                        @else
                            <div class="avatar-badge mx-auto shadow-sm" style="width: 72px; height: 72px; font-size: 1.8rem; background: #94a3b8;">
                                {{ strtoupper(substr($top2->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 start-50 translate-middle-x badge bg-secondary rounded-pill px-2 py-1 shadow-sm">
                            🥈 Top 2
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-truncate">{{ $top2->name }}</h6>
                    <span class="badge rounded-pill text-white mb-2 mx-auto" style="background: {{ $top2->level_badge_color }}; width: fit-content; font-size: 0.72rem;">
                        Lv.{{ $top2->level }} • {{ $top2->level_title }}
                    </span>
                    <div class="d-flex justify-content-around bg-light rounded-3 p-2 small mt-2">
                        <div>
                            <div class="fw-bold text-dark">{{ $top2->published_posts_count }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bài viết</div>
                        </div>
                        <div class="border-start"></div>
                        <div>
                            <div class="fw-bold text-primary">{{ number_format($top2->exp) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Điểm EXP</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top 1 (Quán Quân) -->
            @php $top1 = $topPosters[0] ?? null; @endphp
            @if($top1)
            <div class="col-md-4 col-sm-12 order-1 order-md-2">
                <div class="card border-0 rounded-4 shadow text-center p-4 bg-white position-relative overflow-hidden" style="border: 2px solid #f59e0b !important; transform: scale(1.03);">
                    <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 rounded-bottom-start fw-bold small">
                        👑 QUÁN QUÂN
                    </div>
                    <div class="position-relative d-inline-block mx-auto mb-3 mt-2">
                        @if ($top1->avatar)
                            <img src="{{ asset('storage/' . $top1->avatar) }}" class="rounded-circle shadow" style="width: 88px; height: 88px; object-fit: cover; border: 3px solid #f59e0b;">
                        @else
                            <div class="avatar-badge mx-auto shadow" style="width: 88px; height: 88px; font-size: 2.2rem; background: #f59e0b;">
                                {{ strtoupper(substr($top1->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 start-50 translate-middle-x badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold shadow">
                            🥇 TOP 1
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-truncate">{{ $top1->name }}</h5>
                    <span class="badge rounded-pill text-white mb-3 mx-auto px-3 py-1" style="background: {{ $top1->level_badge_color }}; width: fit-content; font-size: 0.78rem;">
                        <i class="bi bi-star-fill me-1"></i> Lv.{{ $top1->level }} • {{ $top1->level_title }}
                    </span>
                    <div class="d-flex justify-content-around bg-warning bg-opacity-10 rounded-3 p-2 small mt-1">
                        <div>
                            <div class="fs-6 fw-bold text-dark">{{ $top1->published_posts_count }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bài viết</div>
                        </div>
                        <div class="border-start border-warning"></div>
                        <div>
                            <div class="fs-6 fw-bold text-warning-emphasis">{{ number_format($top1->exp) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Điểm EXP</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top 3 -->
            @php $top3 = $topPosters[2] ?? null; @endphp
            @if($top3)
            <div class="col-md-4 col-sm-6 order-3 order-md-3">
                <div class="card border-0 rounded-4 shadow-sm text-center p-3 h-100 bg-white" style="border-top: 4px solid #b45309 !important;">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        @if ($top3->avatar)
                            <img src="{{ asset('storage/' . $top3->avatar) }}" class="rounded-circle shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">
                        @else
                            <div class="avatar-badge mx-auto shadow-sm" style="width: 72px; height: 72px; font-size: 1.8rem; background: #b45309;">
                                {{ strtoupper(substr($top3->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 start-50 translate-middle-x badge rounded-pill px-2 py-1 shadow-sm text-white" style="background: #b45309;">
                            🥉 Top 3
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-truncate">{{ $top3->name }}</h6>
                    <span class="badge rounded-pill text-white mb-2 mx-auto" style="background: {{ $top3->level_badge_color }}; width: fit-content; font-size: 0.72rem;">
                        Lv.{{ $top3->level }} • {{ $top3->level_title }}
                    </span>
                    <div class="d-flex justify-content-around bg-light rounded-3 p-2 small mt-2">
                        <div>
                            <div class="fw-bold text-dark">{{ $top3->published_posts_count }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bài viết</div>
                        </div>
                        <div class="border-start"></div>
                        <div>
                            <div class="fw-bold text-primary">{{ number_format($top3->exp) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Điểm EXP</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Full Leaderboard Table for Posters -->
        <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-list-ol me-2 text-primary"></i>Danh Sách Xếp Hạng Đóng Góp Bài Viết
                </h6>
                <span class="badge bg-light text-muted border rounded-pill px-3 py-1">
                    {{ $topPosters->total() }} thành viên
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 80px;">Thứ Hạng</th>
                            <th scope="col">Thành Viên</th>
                            <th scope="col">Cấp Độ & Danh Hiệu</th>
                            <th scope="col" class="text-center">Bài Xuất Bản</th>
                            <th scope="col" class="text-center">Điểm EXP</th>
                            <th scope="col" style="min-width: 170px;">Tiến Trình Cấp Độ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPosters as $index => $author)
                        @php 
                            $rank = ($topPosters->currentPage() - 1) * $topPosters->perPage() + $index + 1;
                        @endphp
                        <tr class="{{ auth()->id() === $author->id ? 'table-primary table-active' : '' }}">
                            <td class="ps-4">
                                @if($rank === 1)
                                    <span class="badge bg-warning text-dark fs-6 rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">🥇</span>
                                @elseif($rank === 2)
                                    <span class="badge bg-secondary text-white fs-6 rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">🥈</span>
                                @elseif($rank === 3)
                                    <span class="badge text-white fs-6 rounded-circle p-2" style="width: 32px; height: 32px; background: #b45309; display: inline-flex; align-items: center; justify-content: center;">🥉</span>
                                @else
                                    <span class="fw-bold text-muted ps-2">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($author->avatar)
                                        <img src="{{ asset('storage/' . $author->avatar) }}" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <div class="avatar-badge shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                            {{ strtoupper(substr($author->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            {{ $author->name }}
                                            @if(auth()->id() === $author->id)
                                                <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Bạn</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small text-truncate" style="max-width: 280px;">
                                            {{ $author->bio ?? 'Thành viên cộng đồng Tin tức & Blog' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge rounded-pill text-white shadow-sm px-2 py-1" style="background: {{ $author->level_badge_color }}; font-size: 0.72rem;">
                                        Lv.{{ $author->level }}
                                    </span>
                                    <span class="small fw-semibold ms-1 text-dark">{{ $author->level_title }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold fs-6">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i> {{ $author->published_posts_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary fs-6">
                                    {{ number_format($author->exp) }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">EXP</small>
                            </td>
                            <td>
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                    <span>Tiến độ:</span>
                                    <span><strong>{{ $author->exp }}</strong> / {{ $author->next_level_exp }} EXP</span>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: {{ $author->exp_progress }}%; background: {{ $author->level_badge_color }};" 
                                         aria-valuenow="{{ $author->exp_progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy thành viên nào phù hợp với từ khóa "{{ $search }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($topPosters->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-center">
                {{ $topPosters->links() }}
            </div>
            @endif
        </div>

    @else
        <!-- ========================================== -->
        <!-- MỤC 2: XẾP HẠNG LƯỢT TƯƠNG TÁC CAO NHẤT    -->
        <!-- ========================================== -->
        <div class="alert alert-danger bg-danger-subtle border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-fire fs-5"></i>
            </div>
            <div class="small text-dark">
                <div><strong>Tiêu chí xếp hạng mục này (Tính riêng tương tác):</strong> Dành riêng để vinh danh tác giả có bài viết thu hút độc giả nhất! Điểm tương tác tổng hợp từ <strong>Lượt xem mắt đọc</strong> (1 điểm), <strong>Thả icon cảm xúc bài viết</strong> (5 điểm) và <strong>Bình luận thảo luận</strong> (3 điểm).</div>
                <div class="mt-1 text-danger-emphasis" style="font-size: 0.78rem;"><i class="bi bi-shield-x me-1"></i><em>Bảng xếp hạng chỉ dành riêng cho các thành viên cộng đồng & tác giả, không áp dụng cho tài khoản Quản trị viên (Admin).</em></div>
            </div>
        </div>

        <!-- Full Leaderboard Table for Interactions -->
        <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-fire me-2 text-danger"></i>Danh Sách Tác Giả Có Tương Tác Cao Nhất
                </h6>
                <span class="badge bg-light text-muted border rounded-pill px-3 py-1">
                    {{ count($topInteractionsCollection) }} tác giả
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 80px;">Thứ Hạng</th>
                            <th scope="col">Tác Giả</th>
                            <th scope="col">Cấp Độ</th>
                            <th scope="col" class="text-center">👁️ Lượt Xem</th>
                            <th scope="col" class="text-center">❤️ Thả Icon</th>
                            <th scope="col" class="text-center">💬 Bình Luận</th>
                            <th scope="col" class="text-end pe-4" style="min-width: 150px;">⭐ Điểm Tương Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topInteractionsCollection as $index => $author)
                        @php $rank = $index + 1; @endphp
                        <tr class="{{ auth()->id() === $author->id ? 'table-warning table-active' : '' }}">
                            <td class="ps-4">
                                @if($rank === 1)
                                    <span class="badge bg-warning text-dark fs-6 rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">🥇</span>
                                @elseif($rank === 2)
                                    <span class="badge bg-secondary text-white fs-6 rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">🥈</span>
                                @elseif($rank === 3)
                                    <span class="badge text-white fs-6 rounded-circle p-2" style="width: 32px; height: 32px; background: #b45309; display: inline-flex; align-items: center; justify-content: center;">🥉</span>
                                @else
                                    <span class="fw-bold text-muted ps-2">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($author->avatar)
                                        <img src="{{ asset('storage/' . $author->avatar) }}" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <div class="avatar-badge shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem; background: #475569;">
                                            {{ strtoupper(substr($author->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            {{ $author->name }}
                                            @if(auth()->id() === $author->id)
                                                <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Bạn</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            {{ $author->publishedPosts->count() }} bài viết đã xuất bản
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill text-white shadow-sm px-2 py-1" style="background: {{ $author->level_badge_color }}; font-size: 0.72rem;">
                                    Lv.{{ $author->level }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold text-dark">
                                    {{ number_format($author->stat_views) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-bold">
                                    <i class="bi bi-heart-fill me-1"></i> {{ number_format($author->stat_reactions) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1 fw-bold">
                                    <i class="bi bi-chat-dots-fill me-1"></i> {{ number_format($author->stat_comments) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="badge text-white px-3 py-2 rounded-pill shadow-sm" style="background: #e11d48; font-size: 0.85rem;">
                                    <i class="bi bi-stars me-1"></i> {{ number_format($author->stat_score) }} điểm
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không có dữ liệu tương tác phù hợp.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
