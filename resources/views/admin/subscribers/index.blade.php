@extends('layouts.admin')
@section('title', 'Quản lý Người đăng ký Bản tin')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-envelope-paper-fill text-primary me-2"></i>Danh sách Đăng ký Bản tin
            </h4>
            <p class="text-muted small mb-0">Quản lý danh sách email của độc giả đã đăng ký nhận tin tức định kỳ.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold">
                Tổng cộng: {{ $subscribers->total() }} email
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 70px;">#</th>
                    <th scope="col">Địa chỉ Email</th>
                    <th scope="col" style="width: 150px;">Trạng thái</th>
                    <th scope="col" style="width: 180px;">Thời gian đăng ký</th>
                    <th scope="col" style="width: 100px;" class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $index => $sub)
                <tr>
                    <td class="text-muted small">{{ $subscribers->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope text-secondary"></i>
                            <span class="fw-semibold text-dark">{{ $sub->email }}</span>
                        </div>
                    </td>
                    <td>
                        @if($sub->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Đang kích hoạt</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Tạm dừng</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $sub->created_at->format('d/m/Y H:i') }}
                        <div class="text-muted opacity-75" style="font-size: 0.72rem;">{{ $sub->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('admin.subscribers.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa email này khỏi danh sách nhận bản tin?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xóa email">
                                <i class="bi bi-trash3 me-1"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                        <span>Chưa có độc giả nào đăng ký bản tin.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $subscribers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
