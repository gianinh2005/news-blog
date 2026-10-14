@extends('layouts.admin')
@section('title', 'Thêm bài viết mới')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left"></i> Danh sách bài viết</a>
    <h3 class="fw-bold mb-0">Thêm bài viết mới</h3>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li><small>{{ $error }}</small></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Nhập tiêu đề..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tóm tắt ngắn (Summary)</label>
                        <textarea name="summary" class="form-control" rows="3" placeholder="Đoạn mô tả ngắn hiển thị ở trang chủ...">{{ old('summary') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nội dung chi tiết <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="12" placeholder="Soạn thảo nội dung bài viết..." required>{{ old('content') }}</textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0 p-3 mb-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chuyên mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn chuyên mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Loại bài viết <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="blog" {{ old('type') == 'blog' ? 'selected' : '' }}>Blog cá nhân</option>
                                <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>Tin tức</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất bản ngay (Published)</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ảnh đại diện (Thumbnail)</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            <small class="text-muted">Định dạng JPG, PNG, WEBP (Tối đa 2MB)</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-save me-1"></i> Đăng bài viết
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
