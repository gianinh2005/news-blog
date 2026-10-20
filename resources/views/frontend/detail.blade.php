@extends('layouts.app')
@section('title', $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.show', $post->category->slug) }}" class="text-decoration-none">{{ $post->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
            </ol>
        </nav>

        <h1 class="fw-bold mb-3 display-6">{{ $post->title }}</h1>
        <div class="d-flex align-items-center text-muted mb-4 border-bottom pb-3 flex-wrap gap-3">
            <span class="badge {{ $post->type === 'news' ? 'bg-danger' : 'bg-success' }}">{{ strtoupper($post->type) }}</span>
            <span><i class="bi bi-person"></i> {{ $post->user->name }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '' }}</span>
            <span><i class="bi bi-eye"></i> {{ $post->views }} lượt xem</span>
        </div>

        @if($post->thumbnail)
            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="img-fluid rounded mb-4 w-100 shadow-sm" style="max-height: 450px; object-fit: cover;" alt="{{ $post->title }}">
        @endif

        @if($post->summary)
            <div class="lead fw-normal text-secondary mb-4 p-3 bg-light rounded border-start border-4 border-primary">
                {{ $post->summary }}
            </div>
        @endif

        <div class="post-content lh-lg mb-5 fs-5">
            {!! nl2br(e($post->content)) !!}
        </div>

        <!-- Bài viết liên quan -->
        @if($relatedPosts->count() > 0)
            <div class="mb-5">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Bài viết liên quan</h5>
                <div class="row g-3">
                    @foreach($relatedPosts as $rel)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=300&auto=format&fit=crop&q=60' }}"
                                     class="card-img-top" style="height: 120px; object-fit: cover;" alt="">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-0">
                                        <a href="{{ route('post.show', $rel->slug) }}" class="text-dark text-decoration-none small fw-semibold">
                                            {{ Str::limit($rel->title, 50) }}
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ====================================================
             PHẦN BÌNH LUẬN
             - Form gửi comment với validation + counter ký tự
             - Danh sách comment với nút xóa cho chủ sở hữu
             ==================================================== --}}
        <div class="card shadow-sm border-0 mb-5 rounded-3">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">
                    <i class="bi bi-chat-left-text me-2"></i>Bình luận ({{ $post->comments->count() }})
                </h4>

                {{-- Flash message thành công (gửi / xóa bình luận) --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @auth
                    {{-- === FORM GỬI BÌNH LUẬN === --}}
                    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-4" id="comment-form">
                        @csrf
                        <div class="mb-2">
                            <label for="comment-content" class="form-label fw-semibold small">Bình luận của bạn</label>
                            {{-- Textarea: giữ lại nội dung nếu bị lỗi validation --}}
                            <textarea
                                id="comment-content"
                                name="content"
                                class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}"
                                rows="3"
                                placeholder="Chia sẻ suy nghĩ của bạn... (tối thiểu 2, tối đa 1000 ký tự)"
                                maxlength="1000"
                                oninput="updateCharCount(this)"
                            >{{ old('content') }}</textarea>

                            {{-- Hiển thị lỗi validation tiếng Việt --}}
                            @error('content')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror

                            {{-- Counter ký tự realtime --}}
                            <div class="text-end mt-1">
                                <small id="char-count" class="text-muted">0 / 1000 ký tự</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i> Gửi bình luận
                        </button>
                    </form>
                @else
                    {{-- Nhắc đăng nhập nếu chưa xác thực --}}
                    <div class="alert alert-secondary text-center py-3">
                        Vui lòng <a href="{{ route('login') }}" class="fw-bold text-decoration-none">đăng nhập</a> để tham gia bình luận.
                    </div>
                @endauth

                {{-- === DANH SÁCH BÌNH LUẬN === --}}
                <div class="comments-list">
                    @forelse($post->comments as $comment)
                        <div class="d-flex mb-3 pb-3 border-bottom" id="comment-{{ $comment->id }}">
                            {{-- Avatar chữ cái đầu của tên người dùng --}}
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                {{-- Tên + thời gian --}}
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-0 fw-bold">
                                        {{ $comment->user->name }}
                                        <small class="text-muted fw-normal font-monospace" style="font-size: 0.8rem;">
                                            • {{ $comment->created_at->diffForHumans() }}
                                        </small>
                                    </h6>

                                    {{-- Nút xóa: chỉ hiện với chủ sở hữu bình luận --}}
                                    @auth
                                        @if(auth()->id() === $comment->user_id)
                                            <form action="{{ route('comments.destroy', $comment->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-link text-danger p-0 ms-2"
                                                        title="Xóa bình luận của tôi">
                                                    <i class="bi bi-trash3"></i> <span class="small">Xóa</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>

                                {{-- Nội dung bình luận --}}
                                <p class="mb-0 text-secondary mt-1">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0">
                            <i class="bi bi-chat-left d-block fs-3 mb-2 opacity-50"></i>
                            Chưa có bình luận nào. Hãy là người đầu tiên!
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- === SCRIPT: Counter ký tự realtime cho textarea bình luận === --}}
@push('scripts')
<script>
    /**
     * Cập nhật số ký tự đã nhập vào textarea bình luận.
     * Đổi màu khi gần đến giới hạn 1000 ký tự.
     *
     * @param {HTMLTextAreaElement} textarea - Textarea bình luận
     */
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        const max   = 1000;
        const el    = document.getElementById('char-count');

        el.textContent = count + ' / ' + max + ' ký tự';

        // Cảnh báo màu khi gần giới hạn (> 900 ký tự)
        if (count > 900) {
            el.classList.add('text-danger');
            el.classList.remove('text-muted');
        } else {
            el.classList.remove('text-danger');
            el.classList.add('text-muted');
        }
    }

    // Khởi tạo counter nếu có nội dung cũ (old() sau lỗi validation)
    const textarea = document.getElementById('comment-content');
    if (textarea && textarea.value.length > 0) {
        updateCharCount(textarea);
    }
</script>
@endpush
@endsection
