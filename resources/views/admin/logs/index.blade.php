@extends('layouts.admin')

@section('title', 'Nhật ký hoạt động Quản trị')

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i> Nhật ký hoạt động
            </h1>
            <p class="text-muted mb-0">
                Ghi nhận và theo dõi toàn bộ lịch sử thao tác của các Quản trị viên trên hệ thống (Duyệt bài, xóa bình luận, phân quyền,...).
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()?->email === 'admin@gmail.com')
                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                    <i class="bi bi-trash3-fill"></i> Dọn dẹp nhật ký
                </button>
            @endif
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2 rounded-pill">
                Tổng cộng: {{ number_format($stats['total']) }} bản ghi
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Thống kê nhanh theo thẻ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Hôm nay</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['today']) }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-event fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Thao tác Bài viết</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['posts']) }}</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-file-earmark-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Thao tác Bình luận</div>
                        <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($stats['comments']) }}</div>
                    </div>
                    <div class="rounded-3 bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-chat-left-dots fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Thao tác Tài khoản</div>
                        <div class="fs-4 fw-bold text-warning-emphasis mt-1">{{ number_format($stats['users']) }}</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle text-warning-emphasis p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="q" class="form-label small fw-semibold text-muted">Từ khóa tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="q"
                               id="q"
                               class="form-control border-start-0"
                               placeholder="Nội dung, địa chỉ IP, tên admin..."
                               value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="action_type" class="form-label small fw-semibold text-muted">Loại thao tác</label>
                    <select name="action_type" id="action_type" class="form-select">
                        @foreach($actionTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" @selected(request('action_type', 'all') === $typeKey)>
                                {{ $typeLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="user_id" class="form-label small fw-semibold text-muted">Admin thực hiện</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">Tất cả admin</option>
                        @foreach($admins as $adminUser)
                            <option value="{{ $adminUser->id }}" @selected(request('user_id') == $adminUser->id)>
                                {{ $adminUser->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date" class="form-label small fw-semibold text-muted">Ngày thao tác</label>
                    <input type="date"
                           name="date"
                           id="date"
                           class="form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100" title="Tìm kiếm & Lọc">
                        <i class="bi bi-filter"></i>
                    </button>
                    @if(request()->hasAny(['q', 'action_type', 'user_id', 'date']))
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng dữ liệu nhật ký --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-list-columns-reverse text-primary"></i>
                <span>Danh sách nhật ký thao tác</span>
                @if(request()->hasAny(['q', 'action_type', 'user_id', 'date']))
                    <span class="badge bg-secondary-subtle text-secondary fw-normal small">Đang lọc</span>
                @endif
            </div>
            <span class="small text-muted">Trang {{ $logs->currentPage() }} / {{ $logs->lastPage() ?: 1 }}</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 70px;">#ID</th>
                            <th style="min-width: 180px;">Quản trị viên</th>
                            <th style="min-width: 140px;">Hành động</th>
                            <th>Mô tả chi tiết</th>
                            <th style="min-width: 150px;">IP / Thiết bị</th>
                            <th class="pe-4 text-end" style="min-width: 160px;">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $badge = $log->getBadge();
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small fw-semibold">
                                    #{{ $log->id }}
                                </td>

                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center gap-2">
                                            @if($log->user->avatar)
                                                <img src="{{ asset('storage/' . $log->user->avatar) }}"
                                                     class="rounded-circle shadow-sm"
                                                     style="width: 32px; height: 32px; object-fit: cover;"
                                                     alt="{{ $log->user->name }}">
                                            @else
                                                <div class="avatar-badge rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm"
                                                     style="width: 32px; height: 32px; font-size: 0.8rem; background: linear-gradient(135deg, #2563eb, #38bdf8);">
                                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $log->user->name }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-robot me-1"></i> Hệ thống
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $badge['class'] }} rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem;">
                                        <i class="bi {{ $badge['icon'] }}"></i>
                                        <span>{{ $badge['label'] }}</span>
                                    </span>
                                </td>

                                <td>
                                    <div class="text-dark small fw-medium text-break">
                                        {{ $log->description }}
                                    </div>
                                    @if(!empty($log->properties))
                                        <div class="mt-1">
                                            <a class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1"
                                               data-bs-toggle="collapse"
                                               href="#props-{{ $log->id }}"
                                               role="button"
                                               aria-expanded="false"
                                               style="font-size: 0.73rem;">
                                                <i class="bi bi-chevron-down"></i> Xem thông số chi tiết
                                            </a>
                                            <div class="collapse mt-1" id="props-{{ $log->id }}">
                                                <pre class="bg-light p-2 rounded-3 text-secondary mb-0 border" style="font-size: 0.72rem; max-width: 450px; overflow-x: auto;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="small text-dark font-monospace">
                                        <i class="bi bi-geo-alt text-muted me-1"></i>{{ $log->ip_address ?: '127.0.0.1' }}
                                    </div>
                                    @if($log->user_agent)
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 180px;" title="{{ $log->user_agent }}">
                                            <i class="bi bi-browser-chrome me-1"></i>{{ $log->user_agent }}
                                        </div>
                                    @endif
                                </td>

                                <td class="pe-4 text-end">
                                    <div class="fw-semibold text-dark small">
                                        {{ $log->created_at->format('H:i:s d/m/Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Chưa có nhật ký hoạt động nào</h6>
                                        <p class="text-muted small mb-0">Các thao tác duyệt bài, xóa bình luận, phân quyền,... của Admin sẽ tự động được ghi lại tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                    Hiển thị từ {{ $logs->firstItem() }} đến {{ $logs->lastItem() }} trong tổng số {{ $logs->total() }} bản ghi
                </span>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- Modal Dọn dẹp nhật ký (chỉ tài khoản admin@gmail.com) --}}
@if(auth()->user()?->email === 'admin@gmail.com')
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="clearLogsModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i> Dọn dẹp nhật ký hoạt động
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.logs.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Thao tác này sẽ xóa các bản ghi lịch sử hoạt động để giảm dung lượng lưu trữ của hệ thống. Bạn có thể chọn phạm vi muốn xóa:
                    </p>
                    <div class="mb-3">
                        <label for="days" class="form-label small fw-semibold text-dark">Phạm vi xóa</label>
                        <select name="days" id="days" class="form-select">
                            <option value="90">Chỉ xóa bản ghi cũ hơn 90 ngày trước</option>
                            <option value="60">Chỉ xóa bản ghi cũ hơn 60 ngày trước</option>
                            <option value="30">Chỉ xóa bản ghi cũ hơn 30 ngày trước</option>
                            <option value="0" class="text-danger fw-bold">Xóa toàn bộ tất cả nhật ký (Làm trống)</option>
                        </select>
                    </div>
                    <div class="alert alert-warning py-2 px-3 small rounded-3 mb-0">
                        <i class="bi bi-shield-lock-fill me-1"></i> Thao tác chỉ được cấp quyền cho tài khoản cấp cao <strong>admin@gmail.com</strong>.
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">Xác nhận xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
