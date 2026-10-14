@extends('layouts.app')
@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h4 class="card-title text-center fw-bold mb-4">Đăng Ký Tài Khoản</h4>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        @foreach ($errors->all() as $error)
                            <small class="d-block">{{ $error }}</small>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Đăng ký</button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Đã có tài khoản?</span> 
                        <a href="{{ route('login') }}" class="text-decoration-none">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
