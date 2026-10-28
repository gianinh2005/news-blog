<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Trang Bảng Xếp Hạng Thành Viên & Cộng Đồng Tác Giả
     * (Loại trừ tài khoản Quản trị viên - role !== 'admin')
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'posts'); // 'posts' (Đăng bài & Level) hoặc 'interactions' (Tương tác)
        $search = $request->query('q');

        // Đồng bộ điểm EXP và Level cho tất cả users thành viên đang hoạt động
        $allActiveUsers = User::withCount('publishedPosts')
            ->where('is_active', true)
            ->where('role', '!=', 'admin')
            ->get();

        foreach ($allActiveUsers as $user) {
            $user->syncExpAndLevel();
        }

        // --- MỤC 1: XẾP HẠNG ĐĂNG BÀI NHIỀU & ĐIỂM KINH NGHIỆM (EXP / LEVEL) ---
        // Chỉ xếp hạng thành viên (role !== 'admin')
        $postersQuery = User::withCount('publishedPosts')
            ->where('is_active', true)
            ->where('role', '!=', 'admin');

        if (! empty($search)) {
            $postersQuery->where('name', 'like', "%{$search}%");
        }

        $topPosters = $postersQuery
            ->orderByDesc('exp')
            ->orderByDesc('published_posts_count')
            ->paginate(15, ['*'], 'posters_page')
            ->appends(['tab' => 'posts', 'q' => $search]);

        // --- MỤC 2: XẾP HẠNG LƯỢT TƯƠNG TÁC CAO NHẤT (TÍNH RIÊNG) ---
        // Chỉ xếp hạng thành viên (role !== 'admin')
        $interactionsQuery = User::with(['publishedPosts' => function ($q) {
            $q->select('id', 'user_id', 'views');
        }])
            ->where('is_active', true)
            ->where('role', '!=', 'admin');

        if (! empty($search)) {
            $interactionsQuery->where('name', 'like', "%{$search}%");
        }

        $allUsersForInteractions = $interactionsQuery->get();

        // Gắn số liệu tương tác cho từng user
        $topInteractionsCollection = $allUsersForInteractions->map(function ($u) {
            $postIds = $u->publishedPosts->pluck('id');
            $totalViews = (int) $u->publishedPosts->sum('views');
            $totalReactions = (int) PostReaction::whereIn('post_id', $postIds)->count();
            $totalComments = (int) Comment::whereIn('post_id', $postIds)->count();

            // Điểm tương tác: 1 view = 1đ, 1 reaction = 5đ, 1 comment = 3đ
            $score = ($totalViews * 1) + ($totalReactions * 5) + ($totalComments * 3);

            $u->stat_views = $totalViews;
            $u->stat_reactions = $totalReactions;
            $u->stat_comments = $totalComments;
            $u->stat_score = $score;

            return $u;
        })->sortByDesc('stat_score')->values();

        // Thống kê toàn cộng đồng (không tính tài khoản admin trong tổng thành viên)
        $stats = [
            'total_members' => User::where('is_active', true)->where('role', '!=', 'admin')->count(),
            'total_posts' => Post::where('status', 'published')->count(),
            'total_reactions' => PostReaction::count(),
            'total_views' => (int) Post::where('status', 'published')->sum('views'),
        ];

        return view('frontend.members', compact(
            'topPosters',
            'topInteractionsCollection',
            'activeTab',
            'search',
            'stats'
        ));
    }
}
