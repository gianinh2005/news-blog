@extends('layouts.app')
@section('title', 'Trang chủ - Tin tức & Blog cá nhân')

@section('content')

{{-- === HERO BANNER (Chỉ hiện ở trang chủ chính, không lọc) === --}}
@if(!request('q') && !request('type') && !request('page'))
    <div class="hero-banner">
        <div class="hero-tag">
            <i class="bi bi-stars"></i> Tạp chí & Blog số
        </div>
        <h1 class="hero-title">
            Khám phá Tin tức nóng hổi<br>
            & Góc nhìn chuyên sâu đa chiều
        </h1>
        <p class="hero-subtitle">
            Nơi cập nhật dòng chảy thời sự, công nghệ thế giới và chia sẻ những câu chuyện thực tế đầy cảm hứng từ cộng đồng tác giả.
        </p>

        <div class="hero-chips">
            <span class="text-white-50 small align-self-center me-1">Chủ đề nổi bật:</span>
            @foreach($categories->take(4) as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="hero-chip-link">
                    #{{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Cột chính: Danh sách bài viết -->
    <div class="col-lg-8">
        {{-- Header & Thanh lọc loại bài viết --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div class="mb-2 mb-sm-0">
                <h3 class="fw-bold mb-0" style="letter-spacing: -0.02em;">
                    @if(request('q'))
                        <i class="bi bi-search text-primary me-2"></i>Kết quả cho: "{{ request('q') }}"
                    @elseif(request('type') == 'news')
                        <i class="bi bi-lightning-charge text-danger me-2"></i>Bản tin thời sự
                    @elseif(request('type') == 'blog')
                        <i class="bi bi-feather text-success me-2"></i>Góc Blog chia sẻ
                    @else
                        <i class="bi bi-fire text-warning me-2"></i>Bài viết mới nhất
                    @endif
                </h3>
                <span class="text-muted small">Cập nhật liên tục những nội dung giá trị nhất</span>
            </div>

            {{-- Tabs chuyển đổi Tin tức / Blog --}}
            <div class="btn-group p-1 bg-white rounded-pill shadow-sm border">
                <a href="{{ route('home') }}"
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ !request('type') ? 'btn-primary shadow-sm' : 'btn-light border-0 text-secondary' }}">
                    Tất cả
                </a>
                <a href="{{ route('home', ['type' => 'news']) }}"
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ request('type') == 'news' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-secondary' }}">
                    Tin tức
                </a>
                <a href="{{ route('home', ['type' => 'blog']) }}"
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ request('type') == 'blog' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-secondary' }}">
                    Blog
                </a>
            </div>
        </div>

        {{-- Lưới bài viết --}}
        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-md-6">
                    <article class="post-card h-100">
                        {{-- Thumbnail với badge nổi --}}
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

                        {{-- Thân Card --}}
                        <div class="post-card-body">
                            {{-- Danh mục & ngày đăng --}}
                            <div class="post-meta-top">
                                <a href="{{ route('category.show', $post->category->slug) }}" class="post-cat-link">
                                    {{ $post->category->name }}
                                </a>
                                <span>•</span>
                                <span>{{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}</span>
                            </div>

                            {{-- Tiêu đề --}}
                            <h4 class="post-title">
                                <a href="{{ route('post.show', $post->slug) }}">
                                    {{ Str::limit($post->title, 65) }}
                                </a>
                            </h4>

                            {{-- Tóm tắt --}}
                            <p class="post-excerpt">
                                {{ Str::limit($post->summary ?? strip_tags($post->content), 105) }}
                            </p>

                            {{-- Footer Card: Tác giả & lượt xem + thời gian đọc ước tính --}}
                            <div class="post-footer-meta">
                                <span class="author-chip">
                                    <span class="author-avatar-sm">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </span>
                                    <span>{{ Str::limit($post->user->name, 15) }}</span>
                                </span>

                                <div class="d-flex align-items-center gap-3">
                                    <span title="Lượt xem"><i class="bi bi-eye me-1"></i>{{ number_format($post->views) }}</span>
                                    <span title="Thời gian đọc ước tính"><i class="bi bi-clock me-1"></i>{{ max(1, ceil(str_word_count(strip_tags($post->content)) / 200)) }}p</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-3 border">
                        <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-bold text-secondary">Không tìm thấy bài viết nào</h5>
                        <p class="text-muted small">Hãy thử tìm kiếm với từ khóa khác hoặc quay lại trang chủ.</p>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                            Xem tất cả bài viết
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Phân trang --}}
        <div class="mt-4 pt-2 d-flex justify-content-center">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Cột phải (Sidebar): Danh mục, bài đọc nhiều, CTA viết bài -->
    <div class="col-lg-4">
        {{-- Widget Danh mục --}}
        <div class="widget-card">
            <div class="widget-title">
                <span><i class="bi bi-folder2-open text-primary me-2"></i>Chuyên mục</span>
                <span class="badge bg-light text-muted small fw-normal">{{ $categories->count() }} mục</span>
            </div>
            <div class="d-flex flex-column gap-1">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="widget-cat-item">
                        <span>{{ $category->name }}</span>
                        <span class="widget-cat-badge">{{ $category->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Widget Bài viết xem nhiều nhất (Ranked list) --}}
        <div class="widget-card">
            <div class="widget-title">
                <span><i class="bi bi-graph-up-arrow text-danger me-2"></i>Đọc nhiều nhất</span>
                <span class="badge bg-danger-subtle text-danger small">HOT</span>
            </div>

            @foreach($featuredPosts as $index => $featured)
                <a href="{{ route('post.show', $featured->slug) }}" class="ranked-item">
                    <span class="ranked-number">{{ sprintf('%02d', $index + 1) }}</span>
                    <img
                        src="{{ $featured->thumbnail ? asset('storage/' . $featured->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=160&auto=format&fit=crop&q=70' }}"
                        class="ranked-thumb"
                        alt="{{ $featured->title }}"
                        loading="lazy"
                    >
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="ranked-title text-truncate">
                            {{ $featured->title }}
                        </div>
                        <div class="small text-muted d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                            <span><i class="bi bi-eye"></i> {{ number_format($featured->views) }}</span>
                            <span>•</span>
                            <span>{{ $featured->published_at ? $featured->published_at->format('d/m') : '' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Widget Kêu gọi viết bài (CTA) --}}
        <div class="widget-cta">
            <div class="mb-2">
                <i class="bi bi-pencil-fill fs-2"></i>
            </div>
            <h5>Chia sẻ câu chuyện của bạn</h5>
            <p>Trở thành tác giả trên nền tảng, đóng góp những bài viết giá trị tới hàng nghìn độc giả mỗi ngày.</p>
            @auth
                <a href="{{ route('user.posts.create') }}" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Viết bài ngay
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                    Đăng ký làm tác giả
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
