@extends('layouts.admin')
@section('title', 'Quản lý Báo cáo vi phạm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">
            <i class="bi bi-flag-fill text-danger me-2"></i>Danh sách Báo cáo vi phạm
        </h3>
        <p class="text-muted small mb-0">Xem xét phản ánh của người dùng về bài viết hoặc bình luận không phù hợp</p>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold small text-muted me-1"><i class="bi bi-funnel"></i> Trạng thái:</span>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">Tất cả</a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
                   class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                    <i class="bi bi-clock-history"></i> Chờ xử lý
                    @if($countPending > 0)
                        <span class="badge bg-danger ms-1">{{ $countPending }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['status' => 'resolved'])) }}" 
                   class="btn btn-sm {{ request('status') === 'resolved' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-check2-circle"></i> Đã xử lý / Đã gỡ
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['status' => 'dismissed'])) }}" 
                   class="btn btn-sm {{ request('status') === 'dismissed' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <i class="bi bi-slash-circle"></i> Bác bỏ
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold small text-muted">Loại:</span>
                <a href="{{ route('admin.reports.index', array_merge(request()->except('type'))) }}" 
                   class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['type' => 'post'])) }}" 
                   class="btn btn-sm {{ request('type') === 'post' ? 'btn-primary' : 'btn-outline-primary' }}">Bài viết</a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['type' => 'comment'])) }}" 
                   class="btn btn-sm {{ request('type') === 'comment' ? 'btn-primary' : 'btn-outline-primary' }}">Bình luận</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 140px;">Đối tượng</th>
                        <th>Nội dung bị báo cáo</th>
                        <th>Lý do & Mô tả</th>
                        <th>Người báo cáo</th>
                        <th>Trạng thái</th>
                        <th class="text-end" style="width: 250px;">Hành động xử lý</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $target = $report->reportable;
                            $isPost = $target instanceof \App\Models\Post;
                            $isComment = $target instanceof \App\Models\Comment;
                            $author = $target?->user;
                        @endphp
                        <tr>
                            <td><span class="text-muted fw-bold">#{{ $report->id }}</span></td>
                            <td>
                                @if($isPost)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <i class="bi bi-file-earmark-text me-1"></i> Bài viết
                                    </span>
                                @elseif($isComment)
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                        <i class="bi bi-chat-dots me-1"></i> Bình luận
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Đã bị xóa</span>
                                @endif
                            </td>
                            <td>
                                @if($target)
                                    @if($isPost)
                                        <div class="fw-bold text-dark text-truncate mb-1" style="max-width: 280px;">
                                            <a href="{{ route('post.show', $target->slug) }}" target="_blank" class="text-decoration-none text-dark">
                                                {{ $target->title }} <i class="bi bi-box-arrow-up-right small text-muted"></i>
                                            </a>
                                        </div>
                                    @elseif($isComment)
                                        <div class="text-secondary small text-truncate mb-1" style="max-width: 280px;">
                                            "{{ $target->content }}"
                                        </div>
                                        @if($target->post)
                                            <small class="text-muted d-block">
                                                Tại bài viết: <a href="{{ route('post.show', $target->post->slug) }}" target="_blank" class="text-decoration-none">{{ Str::limit($target->post->title, 30) }}</a>
                                            </small>
                                        @endif
                                    @endif

                                    @if($author)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-person me-1"></i>Tác giả: <strong>{{ $author->name }}</strong>
                                            @if(!$author->is_active)
                                                <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 10px;">Đang bị khóa</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Nội dung này không còn tồn tại trên hệ thống</span>
                                @endif
                            </td>
                            <td>
                                <div class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">
                                    {{ $report->reason_label }}
                                </div>
                                @if($report->description)
                                    <div class="small text-secondary text-truncate" style="max-width: 220px;" title="{{ $report->description }}">
                                        {{ $report->description }}
                                    </div>
                                @endif
                                @if($report->admin_note)
                                    <div class="small text-info mt-1" title="{{ $report->admin_note }}">
                                        <i class="bi bi-info-circle me-1"></i>Note: {{ Str::limit($report->admin_note, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $report->user?->name ?? 'Người dùng ẩn danh' }}</div>
                                <small class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                @if($report->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Đã xử lý
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i> Đã bác bỏ
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($report->status === 'pending')
                                    {{-- Nút Gỡ nội dung --}}
                                    @if($target)
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 me-1" 
                                                data-bs-toggle="modal" data-bs-target="#takedownModal{{ $report->id }}" title="Gỡ bỏ nội dung này">
                                            <i class="bi bi-trash3"></i> Gỡ
                                        </button>

                                        {{-- Nút Khóa tài khoản tác giả --}}
                                        @if($author && $author->is_active && $author->id !== auth()->id())
                                            <form action="{{ route('admin.reports.lock-user', $report->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn tạm khóa tài khoản của tác giả này?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-dark px-2 py-1 me-1" title="Khóa tài khoản tác giả">
                                                    <i class="bi bi-lock-fill"></i> Khóa User
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    {{-- Nút Bác bỏ báo cáo --}}
                                    <form action="{{ route('admin.reports.dismiss', $report->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Bác bỏ báo cáo này nếu nội dung không vi phạm?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Bác bỏ báo cáo">
                                            <i class="bi bi-x-lg"></i> Bác bỏ
                                        </button>
                                    </form>

                                    {{-- Modal xác nhận gỡ nội dung --}}
                                    @if($target)
                                    <div class="modal fade text-start" id="takedownModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.reports.take-down', $report->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Gỡ bỏ nội dung vi phạm
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Bạn đang xử lý gỡ: 
                                                            <strong>{{ $isPost ? 'Bài viết: '.$target->title : 'Bình luận: "'.$target->content.'"' }}</strong>
                                                        </p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small">Ghi chú xử lý / Lý do gỡ:</label>
                                                            <textarea name="admin_note" class="form-control" rows="3" required placeholder="Nhập lý do gỡ nội dung này...">Nội dung vi phạm tiêu chuẩn cộng đồng theo báo cáo phản ánh.</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-danger">Xác nhận gỡ bỏ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <span class="text-muted small fst-italic">Đã hoàn tất</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check display-6 d-block mb-2 text-success opacity-75"></i>
                                Không có báo cáo vi phạm nào cần xử lý.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
