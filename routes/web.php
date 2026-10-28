<?php

use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserPostController;
use Illuminate\Support\Facades\Route;

// --- GIAO DIỆN CLIENT ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{slug}', [HomeController::class, 'showCategory'])->name('category.show');
Route::get('/the/{slug}', [HomeController::class, 'showTag'])->name('tag.show');
Route::get('/bai-viet/{slug}', [HomeController::class, 'showPost'])->name('post.show');
Route::post('/posts/{post}/increment-views', [HomeController::class, 'incrementViews']);

// Đăng ký nhận bản tin qua Email
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Bảng xếp hạng thành viên (2 mục: Top đăng bài / EXP và Top tương tác)
Route::get('/thanh-vien', [MemberController::class, 'index'])->name('members.index');

// Thả icon cảm xúc bài viết
Route::post('/bai-viet/{post}/reactions', [ReactionController::class, 'toggle'])
    ->name('posts.reactions.toggle');
Route::get('/bai-viet/{post}/reactions/users', [ReactionController::class, 'users'])
    ->name('posts.reactions.users');

// Bình luận (cần đăng nhập)
Route::post('/bai-viet/{post}/comments', [HomeController::class, 'storeComment'])
    ->middleware('auth')
    ->name('comments.store');

// Xóa bình luận (chủ sở hữu bình luận hoặc Admin mới được xóa)
Route::delete('/comments/{comment}', [HomeController::class, 'destroyComment'])
    ->middleware('auth')
    ->name('comments.destroy');

// Lưu bài viết (Bookmark) & Theo dõi tác giả (cần đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/bai-viet/{post}/bookmark', [BookmarkController::class, 'toggle'])
        ->name('posts.bookmark');

    Route::post('/tac-gia/{author}/follow', [FollowController::class, 'toggle'])
        ->name('authors.follow');

    // Quản lý thông báo
    Route::get('/thong-bao', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/thong-bao/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/thong-bao/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});

// --- AUTHENTICATION ---
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);

    // Quên mật khẩu
    Route::get('/quen-mat-khau', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/quen-mat-khau', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

// Đặt lại mật khẩu từ email (không bọc trong guest middleware để dù đang login hay chưa cũng mở được)
Route::get('/dat-lai-mat-khau/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/dat-lai-mat-khau', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Hồ sơ cá nhân
Route::middleware('auth')->group(function () {
    Route::get('/ho-so', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/ho-so', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/ho-so/doi-mat-khau', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});

// --- BÁO CÁO VI PHẠM (CẦN ĐĂNG NHẬP) ---
Route::post('/reports', [ReportController::class, 'store'])
    ->middleware('auth')
    ->name('reports.store');

// --- NGƯỜI DÙNG VIẾT BLOG CÁ NHÂN (CẦN ĐĂNG NHẬP) ---
Route::middleware('auth')->prefix('tai-khoan')->name('user.')->group(function () {
    Route::resource('posts', UserPostController::class)->except(['show']);
});

// --- TRANG QUẢN TRỊ (ADMIN) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Duyệt & Từ chối bài viết
    Route::patch('posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::patch('posts/{post}/reject', [PostController::class, 'reject'])->name('posts.reject');
    Route::resource('posts', PostController::class)->except(['show']);

    // Quản lý bình luận (xem danh sách + xóa spam)
    Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    // Quản lý người đăng ký nhận bản tin
    Route::get('subscribers', [AdminNewsletterController::class, 'index'])->name('subscribers.index');
    Route::delete('subscribers/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('subscribers.destroy');

    // Xuất dữ liệu Excel / CSV
    Route::get('export/posts', [ExportController::class, 'exportPosts'])->name('export.posts');
    Route::get('export/users', [ExportController::class, 'exportUsers'])->name('export.users');
    Route::get('export/reports', [ExportController::class, 'exportReports'])->name('export.reports');

    // Quản lý tài khoản
    Route::resource('users', AdminUserController::class)
        ->only(['index', 'update']);

    Route::patch('users/{user}/status', [AdminUserController::class, 'toggleStatus'])
        ->name('users.status');

    // Quản lý Báo cáo vi phạm
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::patch('reports/{report}/take-down', [AdminReportController::class, 'takeDownContent'])->name('reports.take-down');
    Route::patch('reports/{report}/lock-user', [AdminReportController::class, 'lockUser'])->name('reports.lock-user');

    // Nhật ký hoạt động (Lịch sử thao tác Admin)
    Route::get('logs', [AdminLogController::class, 'index'])->name('logs.index');
    Route::delete('logs/clear', [AdminLogController::class, 'clear'])->name('logs.clear');
});
