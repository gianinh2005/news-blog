@extends('layouts.app')
@section('title', 'Trang chủ - Tin tức & Blog cá nhân')

@section('content')
<div class="row">
    <!-- Cột chính danh sách bài viết -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                @if(request('q'))
                    Kết quả tìm kiếm cho: "{{ request('q') }}"
                @elseif(request('type') == 'news')
                    Bản tin mới nhất
                @elseif(request('type') == 'blog')
                    Góc Blog cá nhân
                @else
                    Bài viết mới nhất
                @endif
            </h3>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('home') }}" class="btn btn-{{ !request('type') ? 'primary' : 'outline-primary' }}">Tất cả</a>
                <a href="{{ route('home', ['type' => 'news']) }}" class="btn btn-{{ request('type') == 'news' ? 'primary' : 'outline-primary' }}">Tin tức</a>
                <a href="{{ route('home', ['type' => 'blog']) }}" class="btn btn-{{ request('type') == 'blog' ? 'primary' : 'outline-primary' }}">Blog cá nhân</a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-md-6">
                    <div class="card post-card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&auto=format&fit=crop&q=60' }}" 
                                 class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $post->title }}">
                            <span class="badge badge-type {{ $post->type === 'news' ? 'bg-danger' : 'bg-success' }}">
                                {{ $post->type === 'news' ? 'Tin tức' : 'Blog' }}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-1">
                                <a href="{{ route('category.show', $post->category->slug) }}" class="text-decoration-none fw-semibold text-secondary">{{ $post->category->name }}</a> 
                                • {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
                            </small>
                            <h5 class="card-title fw-bold">
                                <a href="{{ route('post.show', $post->slug) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                            </h5>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($post->summary ?? strip_tags($post->content), 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <small class="text-muted"><i class="bi bi-person"></i> {{ $post->user->name }}</small>
                                <small class="text-muted"><i class="bi bi-eye"></i> {{ $post->views }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">Chưa có bài viết nào phù hợp.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Sidebar danh mục & bài nổi bật -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <!-- Danh mục -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Danh mục</h5>
                <div class="list-group list-group-flush">
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', $category->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                            {{ $category->name }}
                            <span class="badge bg-secondary rounded-pill">{{ $category->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bài viết xem nhiều -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Xem nhiều nhất</h5>
                @foreach($featuredPosts as $featured)
                    <div class="d-flex mb-3">
                        <img src="{{ $featured->thumbnail ? asset('storage/' . $featured->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=150&auto=format&fit=crop&q=60' }}" 
                             class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;" alt="">
                        <div>
                            <h6 class="mb-1"><a href="{{ route('post.show', $featured->slug) }}" class="text-dark text-decoration-none fw-semibold">{{ Str::limit($featured->title, 45) }}</a></h6>
                            <small class="text-muted"><i class="bi bi-eye"></i> {{ $featured->views }} lượt xem</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
