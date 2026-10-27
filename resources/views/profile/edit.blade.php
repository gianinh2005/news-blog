@extends('layouts.app')

@section('title', 'Tài khoản & Hồ sơ cá nhân')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Profile Nav Tabs -->
            <ul class="nav nav-pills gap-2 p-1 bg-white rounded-pill shadow-sm border mb-4">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'profile' ? 'active bg-primary text-white' : 'text-secondary' }}" 
                       href="{{ route('profile.edit', ['tab' => 'profile']) }}">
                        <i class="bi bi-person-circle me-1"></i> Thông tin cá nhân
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'saved' ? 'active bg-primary text-white' : 'text-secondary' }}" 
                       href="{{ route('profile.edit', ['tab' => 'saved']) }}">
                        <i class="bi bi-bookmark-heart-fill me-1 text-danger"></i> Bài viết đã lưu 
                        <span class="badge bg-light text-dark rounded-pill ms-1">{{ $user->savedPosts()->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'following' ? 'active bg-primary text-white' : 'text-secondary' }}" 
                       href="{{ route('profile.edit', ['tab' => 'following']) }}">
                        <i class="bi bi-people-fill me-1 text-info"></i> Tác giả đang theo dõi 
                        <span class="badge bg-light text-dark rounded-pill ms-1">{{ $user->following()->count() }}</span>
                    </a>
                </li>
            </ul>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($activeTab === 'saved')
                <!-- TAB 2: BÀI VIẾT ĐÃ LƯU (BOOKMARKS) -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">
                                <i class="bi bi-bookmark-heart-fill text-danger me-2"></i>Bài viết đã lưu yêu thích
                            </h4>
                            <span class="text-muted small">Danh sách những bài viết bạn đã đánh dấu để đọc lại</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">
                            {{ $savedPosts->total() }} bài viết
                        </span>
                    </div>

                    <div class="row g-4">
                        @forelse($savedPosts as $post)
                            <div class="col-md-6" id="saved-post-{{ $post->id }}">
                                <article class="post-card h-100 position-relative">
                                    <div class="post-thumb-wrap">
                                        <a href="{{ route('post.show', $post->slug) }}" class="d-block h-100">
                                            <img
                                                src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&auto=format&fit=crop&q=75' }}"
                                                class="post-thumb"
                                                alt="{{ $post->title }}"
                                            >
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-light rounded-circle shadow position-absolute top-0 end-0 m-2 text-danger" 
                                                title="Bỏ lưu bài viết này"
                                                onclick="toggleBookmark({{ $post->id }}, this); document.getElementById('saved-post-{{ $post->id }}').remove();">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="post-card-body">
                                        <div class="post-meta-top">
                                            <span class="text-primary fw-semibold">{{ $post->category->name }}</span>
                                            <span>•</span>
                                            <span>{{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h5 class="post-title">
                                            <a href="{{ route('post.show', $post->slug) }}">
                                                {{ Str::limit($post->title, 60) }}
                                            </a>
                                        </h5>
                                        <div class="post-footer-meta mt-auto">
                                            <span class="small text-secondary">{{ $post->user->name }}</span>
                                            <a href="{{ route('post.show', $post->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                                Đọc tiếp
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-bookmark-x fs-1 text-muted d-block mb-3"></i>
                                <h5 class="fw-bold text-secondary">Bạn chưa lưu bài viết nào</h5>
                                <p class="text-muted small">Hãy nhấn biểu tượng Bookmark khi đọc bài viết để lưu vào đây.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                    Khám phá bài viết ngay
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($savedPosts->hasPages())
                        <div class="mt-4 pt-2 d-flex justify-content-center">
                            {{ $savedPosts->links() }}
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'following')
                <!-- TAB 3: TÁC GIẢ ĐANG THEO DÕI -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">
                                <i class="bi bi-people-fill text-info me-2"></i>Tác giả bạn đang theo dõi
                            </h4>
                            <span class="text-muted small">Nhận thông báo và bài viết mới nhất từ những cây bút bạn quan tâm</span>
                        </div>
                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 rounded-pill fw-bold">
                            {{ $followingAuthors->total() }} tác giả
                        </span>
                    </div>

                    <div class="row g-3">
                        @forelse($followingAuthors as $author)
                            <div class="col-md-6" id="following-author-{{ $author->id }}">
                                <div class="p-3 border rounded-4 d-flex align-items-center justify-content-between gap-3 bg-light">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($author->avatar)
                                            <img src="{{ asset('storage/' . $author->avatar) }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="avatar-badge shadow-sm" style="width: 48px; height: 48px; font-size: 1.2rem; background: #2563eb;">
                                                {{ strtoupper(substr($author->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $author->name }}</h6>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="badge rounded-pill text-white px-2 py-0" style="background: {{ $author->level_badge_color }}; font-size: 0.68rem;">
                                                    Lv.{{ $author->level }}
                                                </span>
                                                <span class="small text-muted" style="font-size: 0.75rem;">
                                                    {{ $author->published_posts_count }} bài viết
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                onclick="toggleFollow({{ $author->id }}, this); document.getElementById('following-author-{{ $author->id }}').remove();">
                                            Bỏ theo dõi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-person-x fs-1 text-muted d-block mb-3"></i>
                                <h5 class="fw-bold text-secondary">Bạn chưa theo dõi tác giả nào</h5>
                                <p class="text-muted small">Hãy ghé thăm Bảng xếp hạng hoặc các bài viết để nhấn Theo dõi tác giả yêu thích.</p>
                                <a href="{{ route('members.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                    Xem bảng xếp hạng thành viên
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($followingAuthors->hasPages())
                        <div class="mt-4 pt-2 d-flex justify-content-center">
                            {{ $followingAuthors->links() }}
                        </div>
                    @endif
                </div>

            @else
                <!-- TAB 1: THÔNG TIN CÁ NHÂN & ĐỔI MẬT KHẨU -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <!-- Level & Gamification Card -->
                    <div class="card border rounded-4 p-3 mb-4 text-white shadow-sm" style="background: #0f172a; border-color: #1e293b !important;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white" style="width: 56px; height: 56px; background: {{ $user->level_badge_color }};">
                                    <span class="fs-5 fw-bold">Lv.{{ $user->level }}</span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold mb-0 text-white">{{ $user->level_title }}</h5>
                                        <span class="badge bg-warning text-dark rounded-pill px-2" style="font-size: 0.7rem;">Cấp {{ $user->level }}</span>
                                    </div>
                                    <div class="small text-light mt-1" style="opacity: 0.9;">
                                        {{ $user->publishedPosts->count() }} bài xuất bản • {{ number_format($user->exp) }} EXP
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm-end">
                                <a href="{{ route('members.index', ['tab' => 'posts', 'q' => $user->name]) }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                    <i class="bi bi-trophy-fill text-warning me-1"></i> Xem thứ hạng
                                </a>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3 pt-2 border-top border-white border-opacity-10">
                            <div class="d-flex justify-content-between text-light small mb-1" style="opacity: 0.9;">
                                <span>Tiến độ thăng cấp:</span>
                                <span><strong>{{ $user->exp }}</strong> / {{ $user->next_level_exp }} EXP ({{ $user->exp_progress }}%)</span>
                            </div>
                            <div class="progress" style="height: 7px; background: rgba(255,255,255,0.2);">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" 
                                     role="progressbar" 
                                     style="width: {{ $user->exp_progress }}%;" 
                                     aria-valuenow="{{ $user->exp_progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="small text-light mt-2" style="font-size: 0.75rem; opacity: 0.85;">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Mẹo: Đăng thêm bài viết được duyệt để nhận +100 EXP / bài và tăng hạng nhanh chóng!
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small text-secondary">Tên hiển thị</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" maxlength="255" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Email</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <div class="form-text small">Email là định danh tài khoản và không thể thay đổi.</div>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label fw-semibold small text-secondary">Ảnh đại diện</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                                @else
                                    <div class="avatar-badge shadow-sm" style="width: 64px; height: 64px; font-size: 1.5rem; background: #2563eb;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input id="avatar" name="avatar" type="file" class="form-control" accept="image/*">
                                    <div class="form-text small">Định dạng JPG, PNG, WEBP, tối đa 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="form-label fw-semibold small text-secondary">Tiểu sử ngắn (Bio)</label>
                            <textarea id="bio" name="bio" class="form-control" rows="3" maxlength="1000" placeholder="Giới thiệu đôi nét về bản thân bạn...">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Lưu thông tin
                        </button>
                    </form>

                    <hr class="my-5">

                    <h4 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>Đổi mật khẩu
                    </h4>

                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold small text-secondary">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="••••••••">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold small text-secondary">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Tối thiểu 6 ký tự">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-key-fill me-1"></i> Cập nhật mật khẩu
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFollow(authorId, btn) {
        fetch(`/tac-gia/${authorId}/follow`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(console.error);
    }
</script>
@endpush