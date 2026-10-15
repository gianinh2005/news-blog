<?php

use App\Http\Controllers\Admin\CategoryController;
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

// Bình luận (cần đăng nhập)
Route::post('/bai-viet/{post}/comments', [HomeController::class, 'storeComment'])
    ->middleware('auth')
    ->name('comments.store');

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
    Route::resource('posts', PostController::class)->except(['show']);
});
