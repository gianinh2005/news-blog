@extends('layouts.admin')
@section('title', 'Quản lý Bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Danh sách bài viết</h3>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Thêm bài viết mới</a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Loại</th>
                        <th>Lượt xem</th>
                        <th>Trạng thái</th>
                        <th class="text-end" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=100' }}" 
                                     style="width: 55px; height: 42px; object-fit: cover;" class="rounded" alt="">
                            </td>
                            <td>
                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($post->title, 45) }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $post->category->name }}</span></td>
                            <td><span class="badge {{ $post->type == 'news' ? 'bg-danger' : 'bg-success' }}">{{ $post->type }}</span></td>
                            <td>{{ $post->views }}</td>
                            <td><span class="badge {{ $post->status == 'published' ? 'bg-primary' : 'bg-secondary' }}">{{ $post->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có bài viết nào. Hãy tạo bài viết đầu tiên!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $posts->links('pagination::bootstrap-5') }}
</div>
@endsection
