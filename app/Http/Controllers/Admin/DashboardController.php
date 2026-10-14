<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalViews = Post::sum('views');
        $recentPosts = Post::with(['user', 'category'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalPosts', 'totalCategories', 'totalUsers', 'totalViews', 'recentPosts'));
    }
}
