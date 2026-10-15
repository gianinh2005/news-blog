@extends('layouts.app')
@section('title', 'Chỉnh sửa bài viết')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('user.posts.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
                <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary"></i> Chỉnh sửa bài viết</h3>
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

                @if($post->status === 'rejected' && $post->reject_reason)
                    <div class="alert alert-warning py-3 border-start border-4 border-warning mb-4">
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Lý do ban quản trị từ chối bài viết:</h6>
                        <p class="mb-0 text-secondary">{{ $post->reject_reason }}</p>
                        <small class="text-muted fst-italic">Vui lòng điều chỉnh lại nội dung theo yêu cầu và chọn "Gửi duyệt lại".</small>
                    </div>
                @endif

                <form action="{{ route('user.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề blog <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $post->title) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chuyên mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hành động <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status', $post->status) === 'pending' || $post->status === 'rejected' ? 'selected' : '' }}>🚀 Gửi duyệt bài viết (Chờ Admin phê duyệt)</option>
                                <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>💾 Lưu bản nháp (Chưa gửi)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ảnh bìa bài viết</label>
                        @if($post->thumbnail)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="rounded img-thumbnail" style="max-height: 120px;">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        <small class="text-muted">Để trống nếu không muốn đổi ảnh bìa.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tóm tắt ngắn</label>
                        <textarea name="summary" class="form-control" rows="2">{{ old('summary', $post->summary) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nội dung bài viết <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="12" required>{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('user.posts.index') }}" class="btn btn-light">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
