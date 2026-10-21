```blade
@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Thông tin cá nhân</h1>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Đóng"
                            ></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('profile.update') }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên hiển thị
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                maxlength="255"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                value="{{ $user->email }}"
                                disabled
                            >
                            <div class="form-text">
                                Email hiện chưa được phép thay đổi.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">
                                Giới thiệu bản thân
                            </label>
                            <textarea
                                id="bio"
                                name="bio"
                                class="form-control"
                                rows="4"
                                maxlength="1000"
                            >{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Ảnh đại diện hiện tại
                            </label>

                            @if ($user->avatar)
                                <div class="mb-3">
                                    <img
                                        src="{{ asset('storage/' . $user->avatar) }}"
                                        alt="Ảnh đại diện"
                                        width="100"
                                        height="100"
                                        style="object-fit: cover; border-radius: 50%;"
                                    >
                                </div>
                            @else
                                <p class="text-muted">
                                    Bạn chưa có ảnh đại diện.
                                </p>
                            @endif

                            <label for="avatar" class="form-label">
                                Chọn ảnh mới
                            </label>
                            <input
                                id="avatar"
                                name="avatar"
                                type="file"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                            <div class="form-text">
                                JPG, PNG hoặc WebP; dung lượng tối đa 2 MB.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```