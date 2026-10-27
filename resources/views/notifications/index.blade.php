@extends('layouts.app')
@section('title', 'Trung tâm thông báo')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-bell-fill text-primary me-2"></i>Trung tâm thông báo
            </h3>
            <p class="text-muted small mb-0">Cập nhật tương tác, bình luận và phản hồi bài viết của bạn</p>
        </div>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-check2-all me-1"></i> Đánh dấu đã đọc tất cả
                </button>
            </form>
        @endif
    </div>

    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notif)
                <div class="list-group-item p-3 border-bottom d-flex align-items-start gap-3 {{ is_null($notif->read_at) ? 'bg-primary-subtle bg-opacity-25' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        <div class="avatar-badge" style="width: 38px; height: 38px; font-size: 1rem; background: #2563eb;">
                            @if(($notif->data['type'] ?? '') === 'reaction')
                                ❤️
                            @elseif(($notif->data['type'] ?? '') === 'follow')
                                👥
                            @elseif(($notif->data['type'] ?? '') === 'post_approved')
                                🎉
                            @elseif(($notif->data['type'] ?? '') === 'post_rejected')
                                ⚠️
                            @else
                                💬
                            @endif
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-1 text-dark">
                                {{ $notif->data['title'] ?? 'Thông báo' }}
                            </h6>
                            <span class="small text-muted" style="font-size: 0.75rem;">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="mb-2 text-secondary small" style="line-height: 1.5;">
                            {{ $notif->data['message'] ?? '' }}
                        </p>
                        @if(!empty($notif->data['url']))
                            <a href="{{ $notif->data['url'] }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 small fw-semibold text-primary" onclick="markNotifRead('{{ $notif->id }}')">
                                Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                    @if(is_null($notif->read_at))
                        <span class="badge rounded-circle bg-primary p-1" style="width: 8px; height: 8px;" title="Chưa đọc"></span>
                    @endif
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-secondary">Bạn chưa có thông báo nào</h5>
                    <p class="text-muted small">Khi có thành viên tương tác với bài viết của bạn, thông báo sẽ hiển thị ở đây.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
