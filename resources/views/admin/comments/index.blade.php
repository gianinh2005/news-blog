@extends('layouts.admin')
@section('title', 'Quản lý Bình luận')

@section('content')
{{-- ===================================================
     TRANG QUẢN LÝ BÌNH LUẬN (ADMIN)
     Admin xem toàn bộ bình luận, tìm kiếm và xóa spam
     =================================================== --}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">
        <i class="bi bi-chat-left-text me-2 text-primary"></i>Quản lý Bình luận
    </h3>
    {{-- Tổng số bình luận hiện có --}}
    <span class="badge bg-primary fs-6">{{ $totalComments }} bình luận</span>
</div>

{{-- === THANH TÌM KIẾM === --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label for="q" class="form-label fw-semibold small text-muted">
                    Tìm kiếm bình luận
                </label>
                {{-- Input tìm kiếm theo nội dung, tên người dùng hoặc tiêu đề bài viết --}}
                <input
                    type="text"
                    id="q"
                    name="q"
                    class="form-control"
                    placeholder="Nhập từ khóa (nội dung, tên người dùng, tiêu đề bài viết)..."
                    value="{{ request('q') }}"
                >
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
                {{-- Nút xóa bộ lọc --}}
                @if(request('q'))
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- === BẢNG DANH SÁCH BÌNH LUẬN === --}}
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold">
            <i class="bi bi-list-ul me-1"></i>
            Danh sách bình luận
            @if(request('q'))
                <span class="text-muted fw-normal small"> — kết quả tìm kiếm: "{{ request('q') }}"</span>
            @endif
        </span>
        <span class="text-muted small">{{ $comments->total() }} kết quả</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Người bình luận</th>
                        <th style="width: 35%">Nội dung</th>
                        <th style="width: 25%">Bài viết</th>
                        <th style="width: 12%">Thời gian</th>
                        <th style="width: 8%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            {{-- STT theo phân trang --}}
                            <td class="text-muted small">
                                {{ ($comments->currentPage() - 1) * $comments->perPage() + $loop->iteration }}
                            </td>

                            {{-- Thông tin người bình luận --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Avatar chữ cái đầu --}}
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width: 34px; height: 34px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $comment->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $comment->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Nội dung bình luận — giới hạn 120 ký tự --}}
                            <td>
                                <p class="mb-0 small" title="{{ $comment->content }}">
                                    {{ Str::limit($comment->content, 120) }}
                                </p>
                            </td>

                            {{-- Bài viết chứa bình luận --}}
                            <td>
                                <a href="{{ route('post.show', $comment->post->slug) }}"
                                   target="_blank"
                                   class="text-decoration-none small text-primary fw-semibold"
                                   title="{{ $comment->post->title }}">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    {{ Str::limit($comment->post->title, 45) }}
                                </a>
                            </td>

                            {{-- Thời gian đăng bình luận --}}
                            <td>
                                <span class="small text-muted" title="{{ $comment->created_at->format('d/m/Y H:i:s') }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </td>

                            {{-- Nút xóa bình luận spam --}}
                            <td class="text-center">
                                <form action="{{ route('admin.comments.destroy', $comment->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xóa bình luận spam">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        {{-- Trạng thái rỗng --}}
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-left-x fs-1 d-block mb-2 opacity-50"></i>
                                @if(request('q'))
                                    Không tìm thấy bình luận nào khớp với "<strong>{{ request('q') }}</strong>"
                                @else
                                    Chưa có bình luận nào trong hệ thống.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- === PHÂN TRANG === --}}
    @if($comments->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection
