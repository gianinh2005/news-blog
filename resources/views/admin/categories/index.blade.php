@extends('layouts.admin')
@section('title', 'Quản lý Danh mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Danh sách chuyên mục</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Thêm chuyên mục</a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên chuyên mục</th>
                        <th>Đường dẫn (Slug)</th>
                        <th>Mô tả</th>
                        <th>Số bài viết</th>
                        <th class="text-end" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td class="fw-bold text-primary">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ Str::limit($category->description, 50) ?? '---' }}</td>
                            <td><span class="badge bg-secondary rounded-pill">{{ $category->posts_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa chuyên mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có chuyên mục nào. Hãy tạo chuyên mục đầu tiên!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $categories->links('pagination::bootstrap-5') }}
</div>
@endsection
