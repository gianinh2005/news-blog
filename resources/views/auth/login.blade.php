@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h4 class="card-title text-center fw-bold mb-4">Đăng Nhập</h4>
                
                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        @foreach ($errors->all() as $error)
                            <small class="d-block">{{ $error }}</small>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Đăng nhập</button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Chưa có tài khoản?</span> 
                        <a href="{{ route('register') }}" class="text-decoration-none">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
