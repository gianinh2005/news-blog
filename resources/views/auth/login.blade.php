@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="text-center mb-4">
                    <div class="brand-icon mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.35rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Chào mừng trở lại</h4>
                    <p class="text-muted small">Đăng nhập tài khoản của bạn để tiếp tục</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        @foreach ($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold small text-secondary mb-0">Mật khẩu</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted small" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm mb-3">
                        Đăng nhập
                    </button>

                    <div class="text-center pt-2 border-top">
                        <span class="text-muted small">Chưa có tài khoản?</span>
                        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold small text-primary ms-1">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
