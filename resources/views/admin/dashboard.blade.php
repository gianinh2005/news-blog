@extends('layouts.admin')
@section('title', 'Bảng điều khiển quản trị')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Tổng quan hệ thống</h3>
        <p class="text-muted small mb-0">Thống kê hoạt động tin tức, bài viết, cảm xúc tương tác và độc giả</p>
    </div>

    {{-- Toolbar Xuất dữ liệu Excel / CSV --}}
    <div class="d-flex flex-wrap gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle fw-semibold shadow-sm" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1 text-success"></i> Xuất dữ liệu Excel (CSV)
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1 py-1">
                <li>
                    <a class="dropdown-item py-2 small" href="{{ route('admin.export.posts') }}">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Xuất danh sách Bài viết
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 small" href="{{ route('admin.export.users') }}">
                        <i class="bi bi-people text-warning me-2"></i> Xuất danh sách Thành viên
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 small" href="{{ route('admin.export.reports') }}">
                        <i class="bi bi-flag text-danger me-2"></i> Xuất lịch sử Báo cáo vi phạm
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- 5 Thẻ Số liệu thống kê phẳng (Solid clean color cards) --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="stat-card-solid" style="background: #2563eb;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Tổng bài viết</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalPosts) }}</h3>
                </div>
                <div class="stat-icon-wrap" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg">
        <div class="stat-card-solid" style="background: #059669;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Chuyên mục</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalCategories) }}</h3>
                </div>
                <div class="stat-icon-wrap" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg">
        <div class="stat-card-solid" style="background: #d97706;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Thành viên</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h3>
                </div>
                <div class="stat-icon-wrap" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg">
        <div class="stat-card-solid" style="background: #7c3aed;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Lượt xem</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalViews) }}</h3>
                </div>
                <div class="stat-icon-wrap" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-eye-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg">
        <div class="stat-card-solid" style="background: #0d9488;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Bản tin Email</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalSubscribers) }}</h3>
                </div>
                <div class="stat-icon-wrap" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Biểu đồ thống kê trực quan (Chart.js) --}}
<div class="row g-4 mb-4">
    {{-- Biểu đồ đường: Tăng trưởng bài viết 7 ngày qua --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-graph-up text-primary me-2"></i> Lượng bài viết mới 7 ngày qua
                </span>
                <span class="badge bg-primary-subtle text-primary rounded-pill">Theo ngày</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div style="height: 280px; position: relative;">
                    <canvas id="postsLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ tròn: Cơ cấu bài viết theo Chuyên mục --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill text-success me-2"></i> Cơ cấu Chuyên mục
                </span>
                <span class="badge bg-light text-secondary rounded-pill">Tỷ lệ</span>
            </div>
            <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-center">
                <div style="height: 260px; width: 100%; position: relative;">
                    <canvas id="categoriesDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ cột: Thống kê cảm xúc độc giả --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-emoji-smile text-warning me-2"></i> Thống kê Cảm xúc tương tác độc giả (Reactions)
                </span>
                <span class="badge bg-light text-secondary rounded-pill">Tổng hợp</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div style="height: 220px; position: relative;">
                    <canvas id="reactionsBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Posts Table --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Biểu đồ đường lượng bài viết 7 ngày
        const lineCtx = document.getElementById('postsLineChart');
        if (lineCtx) {
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dayLabels) !!},
                    datasets: [{
                        label: 'Bài viết mới',
                        data: {!! json_encode($postsPerDay) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Biểu đồ Doughnut cơ cấu chuyên mục
        const doughnutCtx = document.getElementById('categoriesDoughnutChart');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryCounts) !!},
                        backgroundColor: [
                            '#2563eb',
                            '#059669',
                            '#d97706',
                            '#7c3aed',
                            '#ec4899',
                            '#06b6d4',
                            '#f97316'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // 3. Biểu đồ Bar cảm xúc tương tác
        const barCtx = document.getElementById('reactionsBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($reactionLabels) !!},
                    datasets: [{
                        label: 'Lượt tương tác',
                        data: {!! json_encode($reactionCounts) !!},
                        backgroundColor: [
                            '#3b82f6',
                            '#ef4444',
                            '#f59e0b',
                            '#8b5cf6',
                            '#64748b',
                            '#dc2626'
                        ],
                        borderRadius: 8,
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
