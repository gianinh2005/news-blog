<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalViews = Post::sum('views');
        $totalSubscribers = NewsletterSubscriber::count();
        $recentPosts = Post::with(['user', 'category'])->latest()->take(5)->get();

        // 1. Biểu đồ bài viết mới theo ngày (7 ngày gần nhất)
        $days = collect();
        $postsPerDay = [];
        $dayLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabels[] = $date->format('d/m');
            $postsPerDay[] = Post::whereDate('created_at', $date)->count();
        }

        // 2. Biểu đồ phân bố bài viết theo chuyên mục
        $categoriesChart = Category::withCount('posts')->get();
        $categoryLabels = $categoriesChart->pluck('name')->toArray();
        $categoryCounts = $categoriesChart->pluck('posts_count')->toArray();

        // 3. Biểu đồ cảm xúc tương tác
        $reactionTypes = ['like' => 'Thích', 'love' => 'Yêu thích', 'haha' => 'Haha', 'wow' => 'Wow', 'sad' => 'Buồn', 'angry' => 'Phẫn nộ'];
        $reactionLabels = array_values($reactionTypes);
        $reactionCounts = [];
        foreach (array_keys($reactionTypes) as $type) {
            $reactionCounts[] = PostReaction::where('type', $type)->count();
        }

        return view('admin.dashboard', compact(
            'totalPosts',
            'totalCategories',
            'totalUsers',
            'totalViews',
            'totalSubscribers',
            'recentPosts',
            'dayLabels',
            'postsPerDay',
            'categoryLabels',
            'categoryCounts',
            'reactionLabels',
            'reactionCounts'
        ));
    }
}
