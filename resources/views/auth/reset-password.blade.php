@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu mới')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="text-center mb-4">
                    <div class="brand-icon mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.5rem; background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-shield-check text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Thiết lập mật khẩu mới</h4>
                    <p class="text-muted small">Vui lòng nhập mật khẩu mới an toàn cho tài khoản của bạn</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        @foreach ($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control bg-light border-start-0 rounded-end-pill py-2" value="{{ $email ?? old('email') }}" required readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Tối thiểu 6 ký tự..." required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" name="password_confirmation" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Nhập lại mật khẩu mới..." required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm mb-3">
                        <i class="bi bi-check-lg me-1"></i> Lưu và Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
