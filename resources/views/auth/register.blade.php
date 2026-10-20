@extends('layouts.app')
@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="text-center mb-4">
                    <div class="brand-icon mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.35rem;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Tạo tài khoản mới</h4>
                    <p class="text-muted small">Tham gia cộng đồng tác giả & độc giả ngay hôm nay</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        @foreach ($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Họ và tên</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="name" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="name@example.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Tối thiểu 6 ký tự" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Nhập lại mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <input type="password" name="password_confirmation" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Nhập lại mật khẩu phía trên" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm mb-3">
                        Đăng ký tài khoản
                    </button>

                    <div class="text-center pt-2 border-top">
                        <span class="text-muted small">Đã có tài khoản?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold small text-primary ms-1">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
