
@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Quản lý tài khoản</h1>
            <p class="text-muted mb-0">
                Xem danh sách, phân quyền và quản lý trạng thái tài khoản.
            </p>
        </div>

        <span class="badge bg-primary-subtle text-primary fs-6">
            Tổng: {{ $users->total() }} tài khoản
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Người dùng</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="ps-4">{{ $user->id }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </td>

                                <td>
                                    @if ($user->role === 'admin')
                                        <span class="badge bg-warning-subtle text-warning-emphasis">
                                            Admin
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary">
                                            User
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-success-subtle text-success">
                                            Đang hoạt động
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            Đã khóa
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <div class="d-flex flex-column flex-lg-row justify-content-end gap-2">

                                        {{-- Đổi vai trò --}}
                                        <form
                                            action="{{ route('admin.users.update', $user) }}"
                                            method="POST"
                                            class="d-flex gap-2"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <select
                                                name="role"
                                                class="form-select form-select-sm"
                                                aria-label="Vai trò của {{ $user->name }}"
                                                @disabled(auth()->id() === $user->id)
                                            >
                                                <option value="user" @selected($user->role === 'user')>
                                                    User
                                                </option>
                                                <option value="admin" @selected($user->role === 'admin')>
                                                    Admin
                                                </option>
                                            </select>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-primary text-nowrap"
                                                @disabled(auth()->id() === $user->id)
                                            >
                                                Lưu quyền
                                            </button>
                                        </form>

                                        {{-- Khóa / mở khóa --}}
                                        @if (auth()->id() !== $user->id)
                                            <form
                                                action="{{ route('admin.users.status', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn {{ $user->is_active ? 'khóa' : 'mở khóa' }} tài khoản này?')"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                @if ($user->is_active)
                                                    <button type="submit" class="btn btn-sm btn-outline-danger text-nowrap">
                                                        <i class="bi bi-lock me-1"></i>
                                                        Khóa
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success text-nowrap">
                                                        <i class="bi bi-unlock me-1"></i>
                                                        Mở khóa
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                disabled
                                                title="Không thể tự khóa tài khoản"
                                            >
                                                Tài khoản của bạn
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Chưa có tài khoản nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
