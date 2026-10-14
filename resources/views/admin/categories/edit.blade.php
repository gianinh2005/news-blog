@extends('layouts.admin')
@section('title', 'Chỉnh sửa Chuyên mục')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left"></i> Danh sách chuyên mục</a>
    <h3 class="fw-bold mb-0">Chỉnh sửa chuyên mục</h3>
</div>

<div class="row">
    <div class="col-md-6">
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

                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên chuyên mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Đường dẫn rút gọn (Slug)</label>
                        <input type="text" class="form-control bg-light" value="{{ $category->slug }}" disabled>
                        <small class="text-muted">Slug sẽ tự động cập nhật khi đổi tên chuyên mục</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả ngắn</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-warning px-4 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
