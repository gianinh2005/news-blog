@extends('layouts.app')
@section('title', 'Quên mật khẩu')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="text-center mb-4">
                    <div class="brand-icon mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.5rem; background: linear-gradient(135deg, #f59e0b, #ef4444);">
                        <i class="bi bi-key-fill text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Quên mật khẩu?</h4>
                    <p class="text-muted small">Nhập email tài khoản của bạn để nhận liên kết khôi phục mật khẩu qua hộp thư</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success border-0 rounded-3 small py-3 mb-4 d-flex align-items-start gap-2">
                        <i class="bi bi-envelope-check-fill text-success fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Đã gửi email khôi phục!</strong><br>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        @foreach ($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Địa chỉ Email đăng ký tài khoản</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="form-text small text-muted mt-1">
                            Hệ thống sẽ gửi một email chứa đường dẫn đặt lại mật khẩu an toàn đến hộp thư này.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm mb-3">
                        <i class="bi bi-send-fill me-1"></i> Gửi liên kết đặt lại mật khẩu
                    </button>

                    <div class="text-center pt-2 border-top">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold small text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại trang Đăng nhập
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
