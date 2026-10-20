@extends('layouts.admin')
@section('title', 'Bảng điều khiển quản trị')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Tổng quan hệ thống</h3>
        <p class="text-muted small mb-0">Thống kê hoạt động tin tức, bài viết và tương tác người dùng</p>
    </div>
</div>

{{-- Stat Cards with Modern Gradients --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em;">Tổng bài viết</div>
                    <h2 class="fw-bold mb-0 display-6">{{ number_format($totalPosts) }}</h2>
                </div>
                <div class="stat-icon-wrap">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em;">Chuyên mục</div>
                    <h2 class="fw-bold mb-0 display-6">{{ number_format($totalCategories) }}</h2>
                </div>
                <div class="stat-icon-wrap">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em;">Thành viên</div>
                    <h2 class="fw-bold mb-0 display-6">{{ number_format($totalUsers) }}</h2>
                </div>
                <div class="stat-icon-wrap">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em;">Lượt xem</div>
                    <h2 class="fw-bold mb-0 display-6">{{ number_format($totalViews) }}</h2>
                </div>
                <div class="stat-icon-wrap">
                    <i class="bi bi-eye-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Posts Table --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <span class="fw-bold">
            <i class="bi bi-clock-history text-primary me-2"></i> Bài viết vừa cập nhật
        </span>
        <a href="{{ route('admin.posts.index') }}" class="small text-decoration-none text-primary fw-semibold">
            Xem toàn bộ <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4">Tiêu đề</th>
                        <th>Loại</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th class="pe-4 text-end">Ngày đăng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $p)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('post.show', $p->slug) }}" target="_blank" class="fw-semibold text-dark text-decoration-none">
                                    {{ Str::limit($p->title, 50) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $p->type == 'news' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-2 py-1">
                                    {{ $p->type == 'news' ? 'Tin tức' : 'Blog' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border rounded-pill">{{ $p->category->name }}</span>
                            </td>
                            <td>
                                <span class="small fw-medium">{{ $p->user->name }}</span>
                            </td>
                            <td>
                                @if($p->status == 'published')
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">Đã xuất bản</span>
                                @elseif($p->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1">Chờ duyệt</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">{{ $p->status }}</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Chưa có bài viết nào trong hệ thống.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
