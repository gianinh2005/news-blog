@extends('layouts.admin')
@section('title', 'Quản lý Bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Danh sách bài viết</h3>
        <p class="text-muted small mb-0">Quản lý bài viết tin tức và phê duyệt các bài blog từ người dùng</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Thêm bài viết mới</a>
</div>

<!-- Bộ lọc trạng thái bài viết -->
<div class="card shadow-sm border-0 rounded-3 mb-3">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-semibold small text-muted me-1"><i class="bi bi-funnel"></i> Lọc trạng thái:</span>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">Tất cả</a>
            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                <i class="bi bi-clock-history"></i> Chờ duyệt
                @if($countPending > 0)
                    <span class="badge bg-danger ms-1">{{ $countPending }}</span>
                @endif
            </a>
            <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="btn btn-sm {{ request('status') === 'published' ? 'btn-success' : 'btn-outline-success' }}">
                <i class="bi bi-check-circle"></i> Đã duyệt / Công khai
            </a>
            <a href="{{ route('admin.posts.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="bi bi-x-circle"></i> Bị từ chối
            </a>
            <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="btn btn-sm {{ request('status') === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                <i class="bi bi-file-earmark"></i> Bản nháp
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Ảnh</th>
                        <th>Tiêu đề & Tác giả</th>
                        <th>Danh mục</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Lượt xem</th>
                        <th class="text-end" style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=100' }}" 
                                     style="width: 55px; height: 42px; object-fit: cover;" class="rounded" alt="">
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">
                                    <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="text-decoration-none text-dark" title="Xem bài viết (Preview)">
                                        {{ $post->title }} <i class="bi bi-box-arrow-up-right small text-muted"></i>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $post->user->name ?? 'N/A' }} 
                                    @if($post->user && $post->user->isAdmin())
                                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 10px;">Admin</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 10px;">Thành viên</span>
                                    @endif
                                    • {{ $post->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $post->category->name ?? 'Chưa phân loại' }}</span></td>
                            <td>
                                <span class="badge {{ $post->type == 'news' ? 'bg-danger-subtle text-danger border border-danger' : 'bg-info-subtle text-info-emphasis border border-info' }}">
                                    {{ $post->type == 'news' ? 'Tin tức' : 'Blog' }}
                                </span>
                            </td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i> Đã duyệt</span>
                                @elseif($post->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning"><i class="bi bi-clock-history me-1"></i> Chờ duyệt</span>
                                @elseif($post->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger" title="{{ $post->reject_reason }}"><i class="bi bi-x-circle me-1"></i> Từ chối</span>
                                    @if($post->reject_reason)
                                        <div class="small text-danger text-truncate" style="max-width: 120px;" title="{{ $post->reject_reason }}">{{ $post->reject_reason }}</div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary"><i class="bi bi-file-earmark me-1"></i> Bản nháp</span>
                                @endif
                            </td>
                            <td><small class="text-muted"><i class="bi bi-eye"></i> {{ number_format($post->views) }}</small></td>
                            <td class="text-end">
                                <!-- Nút Duyệt / Từ chối nhanh -->
                                @if($post->status === 'pending' || $post->status === 'rejected')
                                    <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="d-inline" title="Phê duyệt bài viết">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success px-2 py-1"><i class="bi bi-check-lg"></i> Duyệt</button>
                                    </form>
                                @endif

                                @if($post->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $post->id }}" title="Từ chối bài viết">
                                        <i class="bi bi-x-lg"></i> Từ chối
                                    </button>

                                    <!-- Modal nhập lý do từ chối -->
                                    <div class="modal fade text-start" id="rejectModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.posts.reject', $post->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Từ chối bài viết</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Nhập lý do từ chối bài viết: <strong>{{ $post->title }}</strong></p>
                                                        <textarea name="reject_reason" class="form-control" rows="3" placeholder="Ví dụ: Nội dung chưa phù hợp, ảnh bìa mờ, vi phạm quy tắc..." required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Xem bài viết">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy bài viết nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $posts->links('pagination::bootstrap-5') }}
</div>
@endsection
