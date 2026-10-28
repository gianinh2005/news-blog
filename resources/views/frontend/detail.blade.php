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
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small">{{ $post->user->name }}</span>
                            @if(!auth()->check() || auth()->id() !== $post->user_id)
                            <button type="button"
                                    id="btn-follow-top-{{ $post->user_id }}"
                                    onclick="toggleFollow({{ $post->user_id }}, this)"
                                    class="btn btn-xs {{ $isFollowingAuthor ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-2 py-0"
                                    style="font-size: 0.72rem; line-height: 1.6;"
                                    title="{{ $isFollowingAuthor ? 'Bỏ theo dõi tác giả' : 'Theo dõi tác giả' }}">
                                <i class="bi {{ $isFollowingAuthor ? 'bi-check-lg' : 'bi-person-plus' }} me-1"></i>
                                <span class="follow-text">{{ $isFollowingAuthor ? 'Đang theo dõi' : 'Theo dõi' }}</span>
                            </button>
                            @endif
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $post->user->role === 'admin' ? 'Biên tập viên' : 'Tác giả' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 text-muted small">
                    <span title="Ngày đăng">
                        <i class="bi bi-calendar3 text-primary me-1"></i>
                        {{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}
                    </span>
                    <span>•</span>
                    <span title="Thời gian đọc ước tính">
                        <i class="bi bi-clock text-info me-1"></i>
                        {{ max(1, ceil(str_word_count(strip_tags($post->content)) / 200)) }} phút đọc
                    </span>
                    <span>•</span>
                    <span title="Lượt xem bài viết">
                        <i class="bi bi-eye text-success me-1"></i>
                        {{-- Thêm ID post-views-count để JavaScript cập nhật số mắt xem tự động --}}
                        <span id="post-views-count">{{ number_format($post->views) }}</span>
                    </span>
                </div>
            </div>

            {{-- Quick Share & Action Bar --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 border-bottom mb-4">
                <div class="d-flex align-items-center gap-2">
                    {{-- Nút Lưu bài viết (Bookmark) --}}
                    <button type="button" 
                            id="btn-bookmark-{{ $post->id }}"
                            onclick="toggleBookmark({{ $post->id }}, this)" 
                            class="btn btn-sm {{ $isSaved ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} rounded-pill px-3 shadow-sm fw-semibold" 
                            title="{{ $isSaved ? 'Bỏ lưu bài viết' : 'Lưu bài viết xem sau' }}">
                        <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                        <span class="bookmark-text">{{ $isSaved ? 'Đã lưu' : 'Lưu bài' }}</span>
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold me-1">
                        <i class="bi bi-share me-1"></i> Chia sẻ:
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copyArticleUrl(this)" title="Sao chép liên kết">
                        <i class="bi bi-link-45deg me-1"></i> <span id="copy-text">Sao chép</span>
                    </button>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Chia sẻ Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Chia sẻ X / Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    {{-- Nút báo cáo bài viết --}}
                    @auth
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reportPostModal" title="Báo cáo bài viết này">
                        <i class="bi bi-flag me-1"></i> Báo cáo
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Đăng nhập để báo cáo bài viết">
                        <i class="bi bi-flag me-1"></i> Báo cáo
                    </a>
                    @endauth
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
                alt="{{ $post->title }}">
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
        <div class="article-content mb-4">
            {!! nl2br(e($post->content)) !!}
        </div>

        {{-- Thẻ từ khóa bài viết (Tags) --}}
        @if($post->tags && $post->tags->count() > 0)
        <div class="article-tags mb-4 d-flex flex-wrap align-items-center gap-2 pt-3 border-top">
            <span class="text-muted small fw-bold"><i class="bi bi-tags-fill text-primary me-1"></i> Thẻ từ khóa:</span>
            @foreach($post->tags as $tag)
                <a href="{{ route('tag.show', $tag->slug) }}" class="badge bg-light text-secondary border rounded-pill px-3 py-2 text-decoration-none shadow-sm" style="font-size: 0.8rem; font-weight: 500;">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
        @endif

        {{-- KHỐI THẢ ICON CẢM XÚC BÀI VIẾT (REACTIONS) --}}
        <div class="reaction-container mb-5" id="reaction-section">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <span>✨ Cảm xúc của bạn về bài viết?</span>
                    </h5>
                    <p class="text-muted small mb-0">Thả biểu tượng icon để bày tỏ cảm xúc và ủng hộ tác giả bài viết</p>
                </div>
                <div class="text-sm-end">
                    <button type="button" 
                            id="btn-view-reactions" 
                            class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-1 text-decoration-none" 
                            style="font-size: 0.82rem; cursor: pointer; transition: all 0.2s;" 
                            onclick="openReactionsModal()"
                            title="Bấm để xem danh sách người đã thả cảm xúc">
                        <i class="bi bi-people-fill text-primary me-1"></i>
                        <span id="total-reactions-count">{{ number_format($reactionCounts['total']) }}</span> lượt cảm xúc
                    </button>
                </div>
            </div>

            <!-- Reaction Buttons Grid -->
            <div class="d-flex flex-wrap gap-2 justify-content-start" id="reaction-buttons-bar">
                @php
                    $reactionsList = [
                        'like'  => ['icon' => '👍', 'label' => 'Thích'],
                        'love'  => ['icon' => '❤️', 'label' => 'Yêu thích'],
                        'haha'  => ['icon' => '😂', 'label' => 'Haha'],
                        'wow'   => ['icon' => '😮', 'label' => 'Wow'],
                        'sad'   => ['icon' => '😢', 'label' => 'Buồn'],
                        'angry' => ['icon' => '😡', 'label' => 'Phẫn nộ'],
                    ];
                @endphp

                @foreach($reactionsList as $type => $info)
                <button type="button" 
                        class="reaction-btn {{ $userReaction === $type ? 'active ' . $type : '' }}" 
                        data-type="{{ $type }}"
                        onclick="sendReaction('{{ $type }}', this)"
                        title="{{ $info['label'] }}">
                    <span class="reaction-emoji">{{ $info['icon'] }}</span>
                    <span class="d-none d-md-inline reaction-label">{{ $info['label'] }}</span>
                    <span class="reaction-count" id="count-{{ $type }}" onclick="viewReactionUsersByType('{{ $type }}', event)" title="Bấm để xem ai đã thả {{ $info['label'] }}">{{ $reactionCounts[$type] }}</span>
                </button>
                @endforeach
            </div>

            @guest
            <div class="mt-3 pt-2 text-muted small d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                <span>Bạn cần <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Đăng nhập</a> để ghi nhận biểu tượng cảm xúc.</span>
            </div>
            @endguest
        </div>

        <!-- Modal Xem danh sách người đã thả cảm xúc -->
        <div class="modal fade" id="reactionsListModal" tabindex="-1" aria-labelledby="reactionsListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-bottom py-3 px-4 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">✨</span>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="reactionsListModalLabel">
                                Người bày tỏ cảm xúc
                            </h6>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="p-2 border-bottom bg-white">
                        <div class="d-flex gap-1 overflow-x-auto pb-1" id="reactions-filter-tabs" style="scrollbar-width: thin;">
                            <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold reaction-filter-btn active" data-filter="all" onclick="filterReactions('all')">
                                Tất cả <span class="badge rounded-pill bg-light text-dark border ms-1" id="modal-tab-count-all">{{ $reactionCounts['total'] }}</span>
                            </button>
                            @foreach($reactionsList as $type => $info)
                            <button type="button" class="btn btn-sm rounded-pill px-2 fw-semibold reaction-filter-btn text-nowrap" data-filter="{{ $type }}" onclick="filterReactions('{{ $type }}')">
                                <span class="me-1">{{ $info['icon'] }}</span>
                                <span class="badge rounded-pill bg-light text-dark border ms-1" id="modal-tab-count-{{ $type }}">{{ $reactionCounts[$type] }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- User List Body -->
                    <div class="modal-body p-3" id="reactions-modal-body" style="min-height: 250px; max-height: 380px; overflow-y: auto;">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                            <div class="small">Đang tải danh sách...</div>
                        </div>
                    </div>

                    <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="bi bi-clock-history me-1"></i> Danh sách cập nhật gần nhất
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Author Signature Card with Level & XP Gamification & Follow --}}
        <div class="card border-0 bg-white shadow-sm rounded-4 p-4 mb-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if ($post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="rounded-circle shadow-sm" style="width: 56px; height: 56px; object-fit: cover;">
                    @else
                        <div class="avatar-badge shadow-sm" style="width: 56px; height: 56px; font-size: 1.4rem;">
                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h6 class="fw-bold mb-0 text-dark fs-5">{{ $post->user->name }}</h6>
                            <span class="badge rounded-pill text-white shadow-sm" style="background: {{ $post->user->level_badge_color }}; font-size: 0.72rem; padding: 3px 8px;">
                                <i class="bi bi-star-fill me-1"></i> Lv.{{ $post->user->level }} • {{ $post->user->level_title }}
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            {{ $post->user->bio ?? 'Tác giả bài viết trên nền tảng Tin tức & Blog cá nhân.' }}
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 pt-2 pt-md-0 border-top border-md-0">
                    <div class="text-md-end me-2">
                        <div class="small fw-bold text-dark">{{ $post->user->publishedPosts->count() }} bài xuất bản</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <span id="author-followers-count-{{ $post->user_id }}">{{ $post->user->followers->count() }}</span> người theo dõi • {{ number_format($post->user->exp) }} EXP
                        </div>
                    </div>
                    @if(!auth()->check() || auth()->id() !== $post->user_id)
                    <button type="button"
                            id="btn-follow-card-{{ $post->user_id }}"
                            onclick="toggleFollow({{ $post->user_id }}, this)"
                            class="btn btn-sm {{ $isFollowingAuthor ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                        <i class="bi {{ $isFollowingAuthor ? 'bi-check-lg' : 'bi-person-plus' }} me-1"></i>
                        <span class="follow-text">{{ $isFollowingAuthor ? 'Đang theo dõi' : 'Theo dõi' }}</span>
                    </button>
                    @endif
                    <a href="{{ route('members.index', ['tab' => 'posts', 'q' => $post->user->name]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" title="Xem trên Bảng Xếp Hạng">
                        <i class="bi bi-trophy-fill text-warning me-1"></i> Xếp hạng
                    </a>
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
                                loading="lazy">
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

        {{-- PHẦN BÌNH LUẬN --}}
        <div class="comment-box mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-chat-left-dots text-primary me-2"></i>Bình luận
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-1 fs-6">
                        {{ $post->allComments()->count() }}
                    </span>
                </h4>
                <span class="small text-muted">Ý kiến độc giả</span>
            </div>

            @auth
            {{-- Form gửi comment gốc --}}
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
                            required>{{ old('content') }}</textarea>

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

            {{-- Danh sách bình luận gốc và phản hồi lồng nhau --}}
            <div class="comments-list mt-3">
                @forelse($post->comments as $comment)
                <div class="comment-item mb-4" id="comment-{{ $comment->id }}">
                    <div class="comment-avatar">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>

                    <div class="comment-content-wrap flex-grow-1">
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
                                @else
                                {{-- Nút báo cáo bình luận --}}
                                <button type="button" 
                                    class="btn btn-sm btn-link text-secondary p-0 ms-1" 
                                    onclick="openReportCommentModal({{ $comment->id }}, '{{ addslashes(Str::limit($comment->content, 60)) }}')"
                                    title="Báo cáo bình luận này vi phạm">
                                    <i class="bi bi-flag small"></i>
                                </button>
                                @endif
                                @endauth
                            </div>
                        </div>

                        <div class="comment-bubble">
                            <p class="mb-0 text-secondary" style="font-size: 0.92rem; line-height: 1.6;">
                                {{ $comment->content }}
                            </p>
                        </div>

                        {{-- Nút Trả lời và số phản hồi --}}
                        <div class="d-flex align-items-center gap-2 mt-2 ps-1">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none text-primary p-0 fw-semibold" style="font-size: 0.8rem;" onclick="toggleReplyBox({{ $comment->id }})">
                                <i class="bi bi-reply-fill me-1"></i> Trả lời
                            </button>
                            @if($comment->replies && $comment->replies->count() > 0)
                            <span class="text-muted small">•</span>
                            <span class="text-muted small" style="font-size: 0.8rem;">
                                <i class="bi bi-chat-dots me-1"></i> {{ $comment->replies->count() }} phản hồi
                            </span>
                            @endif
                        </div>

                        {{-- Form trả lời bình luận (toggle ẩn/hiện) --}}
                        <div id="reply-box-{{ $comment->id }}" class="mt-2 mb-3 d-none">
                            @auth
                            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="bg-light p-3 rounded-3 border">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="mb-2">
                                    <textarea name="content" class="form-control form-control-sm" rows="2" placeholder="Trả lời bình luận của {{ $comment->user->name }}..." required></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" onclick="toggleReplyBox({{ $comment->id }})">Hủy</button>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Gửi phản hồi</button>
                                </div>
                            </form>
                            @else
                            <div class="p-2 px-3 bg-light rounded-3 border small text-muted">
                                Vui lòng <a href="{{ route('login') }}" class="text-primary fw-bold">đăng nhập</a> để trả lời bình luận.
                            </div>
                            @endauth
                        </div>

                        {{-- Danh sách phản hồi (Replies) lồng nhau --}}
                        @if($comment->replies && $comment->replies->count() > 0)
                        <div class="nested-replies-list ms-3 ms-md-4 mt-3 ps-3 border-start border-2 border-primary-subtle d-flex flex-column gap-3">
                            @foreach($comment->replies as $reply)
                            <div class="reply-item d-flex gap-2" id="comment-{{ $reply->id }}">
                                <div class="comment-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                </div>
                                <div class="comment-content-wrap flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark small">{{ $reply->user->name }}</span>
                                            @if($reply->user_id === $post->user_id)
                                            <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.62rem;">Tác giả</span>
                                            @endif
                                            @if($reply->user->isAdmin())
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill" style="font-size: 0.62rem;">Admin</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </small>
                                            @auth
                                            @if(auth()->id() === $reply->user_id)
                                            <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1" title="Xóa phản hồi">
                                                    <i class="bi bi-trash3 small"></i>
                                                </button>
                                            </form>
                                            @else
                                            <button type="button" class="btn btn-sm btn-link text-secondary p-0 ms-1" onclick="openReportCommentModal({{ $reply->id }}, '{{ addslashes(Str::limit($reply->content, 60)) }}')" title="Báo cáo vi phạm">
                                                <i class="bi bi-flag small"></i>
                                            </button>
                                            @endif
                                            @endauth
                                        </div>
                                    </div>
                                    <div class="comment-bubble py-2 px-3 mt-1 bg-light border">
                                        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.5;">{{ $reply->content }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
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

{{-- MODAL BÁO CÁO BÀI VIẾT --}}
@auth
<div class="modal fade" id="reportPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                <input type="hidden" name="reportable_type" value="post">
                <input type="hidden" name="reportable_id" value="{{ $post->id }}">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-flag-fill me-1"></i> Báo cáo bài viết vi phạm
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Bạn đang gửi báo cáo vi phạm cho bài viết: <br>
                        <strong class="text-dark">{{ $post->title }}</strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lý do báo cáo vi phạm <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">-- Chọn lý do vi phạm --</option>
                            @foreach(\App\Models\Report::REASONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Mô tả chi tiết bổ sung (tùy chọn)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Cung cấp thêm ngữ cảnh hoặc dẫn chứng để quản trị viên dễ dàng xử lý..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL BÁO CÁO BÌNH LUẬN --}}
<div class="modal fade" id="reportCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                <input type="hidden" name="reportable_type" value="comment">
                <input type="hidden" name="reportable_id" id="report-comment-id" value="">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-flag-fill me-1"></i> Báo cáo bình luận vi phạm
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border small text-secondary p-2 mb-3">
                        Nội dung bình luận: <em id="report-comment-preview"></em>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lý do báo cáo vi phạm <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">-- Chọn lý do vi phạm --</option>
                            @foreach(\App\Models\Report::REASONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Mô tả chi tiết bổ sung (tùy chọn)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Cung cấp thêm chi tiết vi phạm..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
    function toggleReplyBox(id) {
        const box = document.getElementById('reply-box-' + id);
        if (box) {
            box.classList.toggle('d-none');
            const ta = box.querySelector('textarea');
            if (ta && !box.classList.contains('d-none')) {
                ta.focus();
            }
        }
    }

    function openReportCommentModal(commentId, previewText) {
        document.getElementById('report-comment-id').value = commentId;
        document.getElementById('report-comment-preview').textContent = previewText;
        const modal = new bootstrap.Modal(document.getElementById('reportCommentModal'));
        modal.show();
    }

    function updateCharCount(textarea) {
        const count = textarea.value.length;
        const max = 1000;
        const el = document.getElementById('char-count');
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

    function sendReaction(type, btn) {
        @guest
            if (confirm('Bạn cần đăng nhập để thả cảm xúc cho bài viết. Bạn có muốn chuyển đến trang Đăng nhập ngay không?')) {
                window.location.href = "{{ route('login') }}";
            }
            return;
        @endguest

        // Hiệu ứng nảy biểu tượng
        btn.classList.add('popping');
        setTimeout(() => btn.classList.remove('popping'), 400);

        fetch("{{ route('posts.reactions.toggle', $post->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            if (!response.ok) {
                throw new Error('Lỗi kết nối');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                const types = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
                types.forEach(t => {
                    const el = document.getElementById('count-' + t);
                    if (el && data.counts[t] !== undefined) {
                        el.textContent = data.counts[t];
                    }
                    const b = document.querySelector(`.reaction-btn[data-type="${t}"]`);
                    if (b) {
                        b.className = 'reaction-btn';
                        if (data.user_reaction === t) {
                            b.classList.add('active', t);
                        }
                    }
                });

                const totalEl = document.getElementById('total-reactions-count');
                if (totalEl && data.counts.total !== undefined) {
                    totalEl.textContent = new Intl.NumberFormat().format(data.counts.total);
                }

                // Cập nhật số đếm trên các tab trong modal nếu có
                const allModalBadge = document.getElementById('modal-tab-count-all');
                if (allModalBadge && data.counts.total !== undefined) {
                    allModalBadge.textContent = data.counts.total;
                }
                types.forEach(t => {
                    const modalBadge = document.getElementById(`modal-tab-count-${t}`);
                    if (modalBadge && data.counts[t] !== undefined) {
                        modalBadge.textContent = data.counts[t];
                    }
                });

                // Nếu modal đang mở, cập nhật lại danh sách người thả cảm xúc
                const modalEl = document.getElementById('reactionsListModal');
                if (modalEl && modalEl.classList.contains('show')) {
                    loadReactionsUsers(currentReactionsFilter);
                }
            }
        })
        .catch(err => console.error('Lỗi thả cảm xúc:', err));
    }

    let reactionsModalInstance = null;
    let currentReactionsFilter = 'all';

    function openReactionsModal(defaultType = 'all') {
        const modalEl = document.getElementById('reactionsListModal');
        if (!modalEl) return;

        if (!reactionsModalInstance) {
            reactionsModalInstance = new bootstrap.Modal(modalEl);
        }
        reactionsModalInstance.show();
        filterReactions(defaultType);
    }

    function viewReactionUsersByType(type, event) {
        if (event) {
            event.stopPropagation();
        }
        openReactionsModal(type);
    }

    function filterReactions(type) {
        currentReactionsFilter = type;
        document.querySelectorAll('.reaction-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-filter') === type) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        loadReactionsUsers(type);
    }

    function loadReactionsUsers(type) {
        const bodyEl = document.getElementById('reactions-modal-body');
        if (!bodyEl) return;

        bodyEl.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                <div class="small">Đang tải danh sách...</div>
            </div>
        `;

        const url = "{{ route('posts.reactions.users', $post->id) }}" + (type !== 'all' ? `?type=${type}` : '');

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (!data.success) {
                bodyEl.innerHTML = `<div class="text-center py-4 text-danger small">Không thể nạp danh sách.</div>`;
                return;
            }

            if (data.counts) {
                const allEl = document.getElementById('modal-tab-count-all');
                if (allEl) allEl.textContent = data.counts.total ?? 0;
                const types = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
                types.forEach(t => {
                    const el = document.getElementById(`modal-tab-count-${t}`);
                    if (el) el.textContent = data.counts[t] ?? 0;
                });
            }

            if (!data.reactions || data.reactions.length === 0) {
                bodyEl.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="fs-1 mb-2">🍃</div>
                        <div class="fw-semibold">Chưa có ai bày tỏ cảm xúc này</div>
                        <div class="small text-secondary">Hãy thả cảm xúc để ủng hộ tác giả!</div>
                    </div>
                `;
                return;
            }

            let html = '<div class="d-flex flex-column gap-1">';
            data.reactions.forEach(r => {
                const avatarHtml = r.user_avatar
                    ? `<img src="${r.user_avatar}" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover;" alt="${r.user_name}">`
                    : `<div class="avatar-badge rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 42px; height: 42px; font-size: 0.95rem; background: linear-gradient(135deg, #4f46e5, #06b6d4);">${r.user_name.charAt(0).toUpperCase()}</div>`;

                const adminBadge = r.user_role === 'admin'
                    ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size: 0.65rem;">Admin</span>`
                    : '';

                const levelBadge = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill" style="font-size: 0.65rem;">Cấp ${r.user_level}</span>`;

                html += `
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 reaction-user-item">
                        <div class="d-flex align-items-center gap-2">
                            <div class="position-relative">
                                ${avatarHtml}
                                <span class="position-absolute bottom-0 end-0 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px; transform: translate(15%, 15%); border: 1px solid #f1f5f9;">
                                    ${r.reaction_icon}
                                </span>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="fw-bold text-dark small">${r.user_name}</span>
                                    ${adminBadge}
                                    ${levelBadge}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <span class="fw-semibold text-secondary">${r.reaction_label}</span> • ${r.created_at_human}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            bodyEl.innerHTML = html;
        })
        .catch(err => {
            console.error('Lỗi tải danh sách thả cảm xúc:', err);
            bodyEl.innerHTML = `<div class="text-center py-4 text-danger small">Không thể kết nối đến máy chủ.</div>`;
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        fetch('/posts/{{ $post->id }}/increment-views', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi HTTP status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.views) {
                    // Cập nhật số mắt xem trên màn hình ngay lập tức
                    const viewsElement = document.getElementById('post-views-count');
                    if (viewsElement) {
                        viewsElement.textContent = data.views;
                    }
                }
            })
            .catch(error => console.error('Lỗi tăng lượt xem:', error));
    });
</script>
@endpush
@endsection