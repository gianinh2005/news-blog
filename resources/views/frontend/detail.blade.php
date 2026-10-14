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

        <!-- Bình luận -->
        <div class="card shadow-sm border-0 mb-5 rounded-3">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Bình luận ({{ $post->comments->count() }})</h4>

                @auth
                    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" placeholder="Chia sẻ suy nghĩ của bạn..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Gửi bình luận</button>
                    </form>
                @else
                    <div class="alert alert-secondary text-center py-3">
                        Vui lòng <a href="{{ route('login') }}" class="fw-bold text-decoration-none">đăng nhập</a> để tham gia bình luận.
                    </div>
                @endauth

                <div class="comments-list">
                    @forelse($post->comments as $comment)
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-bold">{{ $comment->user->name }} 
                                    <small class="text-muted fw-normal font-monospace" style="font-size: 0.8rem;">• {{ $comment->created_at->diffForHumans() }}</small>
                                </h6>
                                <p class="mb-0 text-secondary mt-1">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
