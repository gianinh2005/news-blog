<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserPostController;
use Illuminate\Support\Facades\Route;

// --- GIAO DIỆN CLIENT ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{slug}', [HomeController::class, 'showCategory'])->name('category.show');
Route::get('/bai-viet/{slug}', [HomeController::class, 'showPost'])->name('post.show');
Route::post('/posts/{post}/increment-views', [HomeController::class, 'incrementViews']);

// Bình luận (cần đăng nhập)
Route::post('/bai-viet/{post}/comments', [HomeController::class, 'storeComment'])
    ->middleware('auth')
    ->name('comments.store');

// Xóa bình luận (chỉ chủ sở hữu bình luận mới được xóa)
Route::delete('/comments/{comment}', [HomeController::class, 'destroyComment'])
    ->middleware('auth')
    ->name('comments.destroy');

// --- AUTHENTICATION ---
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);
});
Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

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
});
