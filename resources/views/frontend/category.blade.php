@extends('layouts.app')
@section('title', 'Chuyên mục: ' . $category->name)

@section('content')
{{-- Category Header Banner --}}
<div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5 mb-4 position-relative overflow-hidden">
    <div class="position-absolute top-0 end-0 p-4 opacity-10 d-none d-md-block pointer-events-none">
        <i class="bi bi-folder-fill" style="font-size: 8rem; color: var(--primary);"></i>
    </div>
    <div class="position-relative">
        <div class="d-inline-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                <i class="bi bi-folder2-open me-1"></i> Chuyên mục
            </span>
            <span class="text-muted small">• {{ $posts->total() }} bài viết</span>
        </div>
        <h2 class="fw-bold display-6 mb-2 text-dark">{{ $category->name }}</h2>
        <p class="text-secondary lead fs-6 mb-0" style="max-width: 650px;">
            {{ $category->description ?? 'Tổng hợp các bài viết phân tích, tin tức và chia sẻ nổi bật nhất trong chuyên mục ' . $category->name . '.' }}
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Cột chính danh sách bài viết -->
    <div class="col-lg-8">
        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-md-6">
                    <article class="post-card h-100">
                        <div class="post-thumb-wrap">
                            <a href="{{ route('post.show', $post->slug) }}" class="d-block h-100">
                                <img
                                    src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&auto=format&fit=crop&q=75' }}"
                                    class="post-thumb"
                                    alt="{{ $post->title }}"
                                    loading="lazy"
                                >
                            </a>

                            <span class="badge-glass {{ $post->type === 'news' ? 'badge-news' : 'badge-blog' }}">
                                <i class="bi {{ $post->type === 'news' ? 'bi-lightning-fill' : 'bi-feather' }} me-1"></i>
                                {{ $post->type === 'news' ? 'Tin tức' : 'Blog' }}
                            </span>
                        </div>

                        <div class="post-card-body">
                            <div class="post-meta-top">
                                <span>{{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                            </div>

                            <h4 class="post-title">
                                <a href="{{ route('post.show', $post->slug) }}">
                                    {{ Str::limit($post->title, 65) }}
                                </a>
                            </h4>

                            <p class="post-excerpt">
                                {{ Str::limit($post->summary ?? strip_tags($post->content), 105) }}
                            </p>

                            <div class="post-footer-meta">
                                <span class="author-chip">
                                    <span class="author-avatar-sm">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </span>
                                    <span>{{ Str::limit($post->user->name, 15) }}</span>
                                </span>

                                <div class="d-flex align-items-center gap-3">
                                    <span title="Lượt xem"><i class="bi bi-eye me-1"></i>{{ number_format($post->views) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-3 border">
                        <i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-bold text-secondary">Chuyên mục này hiện chưa có bài viết nào</h5>
                        <p class="text-muted small">Hãy quay lại sau hoặc khám phá các chuyên mục khác.</p>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                            Quay về trang chủ
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4 pt-2 d-flex justify-content-center">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Sidebar: Danh mục khác & Kêu gọi viết bài -->
    <div class="col-lg-4">
        <div class="widget-card">
            <div class="widget-title">
                <span><i class="bi bi-folder2 text-primary me-2"></i>Chuyên mục khác</span>
            </div>
            <div class="d-flex flex-column gap-1">
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}"
                       class="widget-cat-item {{ $cat->id === $category->id ? 'fw-bold text-primary bg-light' : '' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="widget-cat-badge">{{ $cat->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="widget-cta">
            <div class="mb-2">
                <i class="bi bi-lightbulb-fill fs-2"></i>
            </div>
            <h5>Đóng góp bài viết</h5>
            <p>Bạn có kiến thức hoặc trải nghiệm muốn chia sẻ trong chủ đề này? Hãy viết bài ngay hôm nay.</p>
            <a href="{{ route('user.posts.create') }}" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                Bắt đầu viết
            </a>
        </div>
    </div>
</div>
@endsection
