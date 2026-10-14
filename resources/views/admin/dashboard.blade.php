@extends('layouts.admin')
@section('title', 'Bảng điều khiển quản trị')

@section('content')
<h3 class="fw-bold mb-4">Tổng quan hệ thống</h3>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-primary text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Tổng bài viết</h6>
                    <h2 class="fw-bold mb-0">{{ $totalPosts }}</h2>
                </div>
                <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-success text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Danh mục</h6>
                    <h2 class="fw-bold mb-0">{{ $totalCategories }}</h2>
                </div>
                <i class="bi bi-tags fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-warning text-dark p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Thành viên</h6>
                    <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                </div>
                <i class="bi bi-people fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-info text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Lượt xem</h6>
                    <h2 class="fw-bold mb-0">{{ $totalViews }}</h2>
                </div>
                <i class="bi bi-eye fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-clock-history me-1"></i> Bài viết vừa cập nhật
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $p)
                        <tr>
                            <td class="fw-semibold">{{ Str::limit($p->title, 45) }}</td>
                            <td><span class="badge {{ $p->type == 'news' ? 'bg-danger' : 'bg-success' }}">{{ $p->type }}</span></td>
                            <td>{{ $p->category->name }}</td>
                            <td>{{ $p->user->name }}</td>
                            <td><span class="badge {{ $p->status == 'published' ? 'bg-primary' : 'bg-secondary' }}">{{ $p->status }}</span></td>
                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có bài viết nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
