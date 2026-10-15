@extends('layouts.app')
@section('title', 'Viết bài blog mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('user.posts.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left"></i> Danh sách blog của tôi
                </a>
                <h3 class="fw-bold mb-0"><i class="bi bi-feather text-primary"></i> Viết bài blog mới</h3>
            </div>
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

                <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề blog <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title') }}" placeholder="Nhập tiêu đề bài viết của bạn..." required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chuyên mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn chủ đề bài viết --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái xuất bản <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Công khai (Xuất bản ngay)</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Lưu bản nháp</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ảnh bìa bài viết (Thumbnail)</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        <small class="text-muted">Định dạng hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tóm tắt ngắn</label>
                        <textarea name="summary" class="form-control" rows="2" placeholder="Một hoặc hai câu tóm tắt nội dung hấp dẫn để hiển thị ngoài danh sách...">{{ old('summary') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nội dung bài viết <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="12" placeholder="Chia sẻ suy nghĩ, câu chuyện hoặc kiến thức của bạn tại đây..." required>{{ old('content') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('user.posts.index') }}" class="btn btn-light">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-send-fill me-1"></i> Đăng bài viết
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
