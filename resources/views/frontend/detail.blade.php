@extends('layouts.app')
@section('title', $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        {{-- Breadcrumb Navigation --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small p-2 px-3 bg-white rounded-pill border shadow-sm align-items-center mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door-fill text-primary"></i> Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', $post->category->slug) }}" class="text-decoration-none text-muted">
                        {{ $post->category->name }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-truncate text-secondary fw-semibold" aria-current="page" style="max-width: 280px;">
                    {{ $post->title }}
                </li>
            </ol>
        </nav>

        {{-- Article Header --}}
        <header class="article-header">
            {{-- Category badge & post type --}}
            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="{{ route('category.show', $post->category->slug) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-decoration-none px-3 py-1 fw-bold">
                    {{ $post->category->name }}
                </a>
                <span class="badge {{ $post->type === 'news' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} rounded-pill px-3 py-1 fw-bold">
                    <i class="bi {{ $post->type === 'news' ? 'bi-lightning-fill' : 'bi-feather' }} me-1"></i>
                    {{ strtoupper($post->type) }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="article-title">{{ $post->title }}</h1>

            {{-- Author Bar & Meta Info --}}
            <div class="article-meta-bar">
                <div class="d-flex align-items-center gap-2 me-auto">
                    <div class="avatar-badge" style="width: 38px; height: 38px; font-size: 0.95rem;">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">{{ $post->user->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $post->user->role === 'admin' ? 'Biên tập viên' : 'Tác giả' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 text-muted small">
                    <span title="Ngày đăng">
                        <i class="bi bi-calendar3 text-primary me-1"></i>
                        {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y') }}
                    </span>
                    <span>•</span>
                    <span title="Thời gian đọc ước tính">
                        <i class="bi bi-clock text-info me-1"></i>
                        {{ max(1, ceil(str_word_count(strip_tags($post->content)) / 200)) }} phút đọc
                    </span>
                    <span>•</span>
                    <span title="Lượt xem bài viết">
                        <i class="bi bi-eye text-success me-1"></i>
                        {{ number_format($post->views) }}
                    </span>
                </div>
            </div>

            {{-- Quick Share Bar --}}
            <div class="d-flex align-items-center justify-content-between py-2 border-bottom mb-4">
                <span class="small text-muted fw-semibold">
                    <i class="bi bi-share me-1"></i> Chia sẻ:
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copyArticleUrl(this)" title="Sao chép liên kết">
                        <i class="bi bi-link-45deg me-1"></i> <span id="copy-text">Sao chép</span>
                    </button>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Chia sẻ Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Chia sẻ X / Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>
        </header>

        {{-- Featured Thumbnail --}}
        @if($post->thumbnail)
            <div class="mb-4">
                <img
                    src="{{ asset('storage/' . $post->thumbnail) }}"
                    class="img-fluid rounded-4 w-100 shadow-sm"
                    style="max-height: 480px; object-fit: cover;"
                    alt="{{ $post->title }}"
                >
            </div>
        @endif

        {{-- Lead / Summary --}}
        @if($post->summary)
            <div class="article-lead">
                <i class="bi bi-quote fs-3 text-primary d-block mb-1 opacity-75"></i>
                {{ $post->summary }}
            </div>
        @endif

        {{-- Main Article Content --}}
        <div class="article-content mb-5">
            {!! nl2br(e($post->content)) !!}
        </div>

        {{-- Author Signature Card --}}
        <div class="card border-0 bg-white shadow-sm rounded-4 p-4 mb-5">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-badge" style="width: 52px; height: 52px; font-size: 1.3rem;">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="fw-bold mb-1">{{ $post->user->name }}</h6>
                    <p class="text-muted small mb-0">
                        {{ $post->user->bio ?? 'Tác giả bài viết trên nền tảng Tin tức & Blog cá nhân.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Bài viết liên quan --}}
        @if($relatedPosts->count() > 0)
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-collection text-primary me-2"></i>Bài viết cùng chuyên mục
                    </h5>
                    <a href="{{ route('category.show', $post->category->slug) }}" class="small text-decoration-none text-primary fw-semibold">
                        Xem thêm <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @foreach($relatedPosts as $rel)
                        <div class="col-md-4">
                            <article class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden post-card">
                                <div style="height: 130px; overflow: hidden;">
                                    <img
                                        src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=360&auto=format&fit=crop&q=70' }}"
                                        class="post-thumb"
                                        alt="{{ $rel->title }}"
                                        loading="lazy"
                                    >
                                </div>
                                <div class="card-body p-3 d-flex flex-column">
                                    <small class="text-muted mb-1" style="font-size: 0.75rem;">
                                        {{ $rel->published_at ? $rel->published_at->format('d/m/Y') : '' }}
                                    </small>
                                    <h6 class="card-title fw-bold mb-0">
                                        <a href="{{ route('post.show', $rel->slug) }}" class="text-dark text-decoration-none small">
                                            {{ Str::limit($rel->title, 55) }}
                                        </a>
                                    </h6>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ====================================================
             PHẦN BÌNH LUẬN HIỆN ĐẠI
             ==================================================== --}}
        <div class="comment-box mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-chat-left-dots text-primary me-2"></i>Bình luận
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-1 fs-6">
                        {{ $post->comments->count() }}
                    </span>
                </h4>
                <span class="small text-muted">Ý kiến độc giả</span>
            </div>

            @auth
                {{-- Form gửi comment --}}
                <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-4" id="comment-form">
                    @csrf
                    <div class="d-flex gap-3 mb-2">
                        <div class="avatar-badge flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <textarea
                                id="comment-content"
                                name="content"
                                class="form-control comment-textarea {{ $errors->has('content') ? 'is-invalid' : '' }}"
                                rows="3"
                                placeholder="Chia sẻ cảm nghĩ hoặc đặt câu hỏi về bài viết này... (tối thiểu 2, tối đa 1000 ký tự)"
                                maxlength="1000"
                                oninput="updateCharCount(this)"
                                required
                            >{{ old('content') }}</textarea>

                            @error('content')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center ps-sm-5 mt-2">
                        <small id="char-count" class="text-muted fw-semibold">0 / 1000 ký tự</small>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Gửi bình luận
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-4 bg-light rounded-3 mb-4 border">
                    <i class="bi bi-chat-heart text-primary fs-2 d-block mb-2"></i>
                    <h6 class="fw-bold mb-1">Bạn cần đăng nhập để tham gia thảo luận</h6>
                    <p class="text-muted small mb-3">Đăng nhập tài khoản để gửi bình luận và tương tác cùng các độc giả khác.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold me-2 shadow-sm">
                        Đăng nhập ngay
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        Tạo tài khoản
                    </a>
                </div>
            @endauth

            {{-- Danh sách bình luận --}}
            <div class="comments-list mt-3">
                @forelse($post->comments as $comment)
                    <div class="comment-item" id="comment-{{ $comment->id }}">
                        <div class="comment-avatar">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>

                        <div class="comment-content-wrap">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark small">{{ $comment->user->name }}</span>
                                    @if($comment->user_id === $post->user_id)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.65rem;">Tác giả</span>
                                    @endif
                                    @if($comment->user->isAdmin())
                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill" style="font-size: 0.65rem;">Admin</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>

                                    {{-- Nút xóa cho người viết comment --}}
                                    @auth
                                        @if(auth()->id() === $comment->user_id)
                                            <form action="{{ route('comments.destroy', $comment->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-link text-danger p-0 ms-1"
                                                        title="Xóa bình luận của bạn">
                                                    <i class="bi bi-trash3 small"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>

                            <div class="comment-bubble">
                                <p class="mb-0 text-secondary" style="font-size: 0.92rem; line-height: 1.6;">
                                    {{ $comment->content }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-left-dots fs-3 d-block mb-2 opacity-50"></i>
                        <span>Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nghĩ!</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        const max   = 1000;
        const el    = document.getElementById('char-count');
        el.textContent = count + ' / ' + max + ' ký tự';

        if (count > 900) {
            el.classList.add('text-danger');
            el.classList.remove('text-muted');
        } else {
            el.classList.remove('text-danger');
            el.classList.add('text-muted');
        }
    }

    function copyArticleUrl(button) {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const span = document.getElementById('copy-text');
            const original = span.textContent;
            span.textContent = 'Đã sao chép!';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-secondary');

            setTimeout(() => {
                span.textContent = original;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        });
    }

    const textarea = document.getElementById('comment-content');
    if (textarea && textarea.value.length > 0) {
        updateCharCount(textarea);
    }
</script>
@endpush
@endsection
