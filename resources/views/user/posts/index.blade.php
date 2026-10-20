@extends('layouts.app')
@section('title', 'Quản lý bài viết của tôi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-journal-text text-primary me-2"></i>Blog của tôi</h3>
                <p class="text-muted small mb-0">Quản lý và xuất bản các bài viết, chia sẻ cá nhân của bạn</p>
            </div>
            <a href="{{ route('user.posts.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Viết bài mới
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4" style="width: 75px;">Ảnh</th>
                                <th>Tiêu đề bài viết</th>
                                <th>Chuyên mục</th>
                                <th>Trạng thái</th>
                                <th>Lượt xem</th>
                                <th>Ngày đăng</th>
                                <th class="pe-4 text-end" style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td class="ps-4">
                                        @if($post->thumbnail)
                                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="rounded-3 object-fit-cover shadow-sm" width="55" height="42">
                                        @else
                                            <div class="bg-light border rounded-3 text-center text-muted d-flex align-items-center justify-content-center" style="width: 55px; height: 42px; font-size: 14px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 320px;">
                                            @if($post->status === 'published')
                                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="text-decoration-none text-dark">
                                                    {{ $post->title }} <i class="bi bi-box-arrow-up-right small text-muted"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">{{ $post->title }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border rounded-pill">{{ $post->category->name ?? 'Chưa phân loại' }}</span></td>
                                    <td>
                                        @if($post->status === 'published')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i> Đã duyệt</span>
                                        @elseif($post->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1"><i class="bi bi-clock-history me-1"></i> Chờ duyệt</span>
                                        @elseif($post->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i> Bị từ chối</span>
                                            @if($post->reject_reason)
                                                <div class="small text-danger mt-1 text-truncate" style="max-width: 140px;" title="{{ $post->reject_reason }}">
                                                    Lý do: {{ $post->reject_reason }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1"><i class="bi bi-file-earmark me-1"></i> Bản nháp</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted"><i class="bi bi-eye"></i> {{ number_format($post->views) }}</small></td>
                                    <td><small class="text-muted">{{ $post->created_at->format('d/m/Y') }}</small></td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('user.posts.edit', $post->id) }}" class="btn btn-outline-primary btn-sm rounded-circle me-1" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('user.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted mb-3">
                                            <i class="bi bi-journal-x display-5 text-muted opacity-50"></i>
                                            <p class="mt-2 mb-0">Bạn chưa có bài viết blog nào.</p>
                                        </div>
                                        <a href="{{ route('user.posts.create') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            <i class="bi bi-pencil-square me-1"></i> Viết bài đầu tiên ngay
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($posts->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
