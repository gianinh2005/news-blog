@extends('layouts.app')
@section('title', 'Chuyên mục: ' . $category->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="mb-4">
            <h3 class="fw-bold mb-1"><i class="bi bi-folder2-open text-primary"></i> {{ $category->name }}</h3>
            <p class="text-muted">{{ $category->description ?? 'Tổng hợp các bài viết trong chuyên mục ' . $category->name }}</p>
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
                                {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
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
                    <div class="alert alert-info text-center py-4">Chuyên mục này hiện chưa có bài viết nào.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Danh mục khác</h5>
                <div class="list-group list-group-flush">
                    @foreach($categories as $cat)
                        <a href="{{ route('category.show', $cat->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 {{ $cat->id === $category->id ? 'fw-bold text-primary' : '' }}">
                            {{ $cat->name }}
                            <span class="badge bg-secondary rounded-pill">{{ $cat->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
