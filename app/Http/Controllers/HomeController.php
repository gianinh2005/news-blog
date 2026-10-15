<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user'])
            ->where('status', 'published')
            ->latest('published_at');

        // Tìm kiếm từ khóa
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo loại (news hoặc blog)
        if ($request->filled('type') && in_array($request->type, ['news', 'blog'])) {
            $query->where('type', $request->type);
        }

        $posts = $query->paginate(9)->withQueryString();
        $featuredPosts = Post::where('status', 'published')->orderBy('views', 'desc')->take(5)->get();
        $categories = Category::withCount('posts')->get();

        return view('frontend.index', compact('posts', 'featuredPosts', 'categories'));
    }

    public function showCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()
            ->with(['user'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::withCount('posts')->get();

        return view('frontend.category', compact('category', 'posts', 'categories'));
    }

    public function showPost(Request $request, string $slug)
    {
        $post = Post::with(['category', 'user', 'comments.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Nếu bài chưa xuất bản, chỉ cho phép tác giả bài viết hoặc Admin xem trước (preview)
        if ($post->status !== 'published') {
            if (! auth()->check() || (auth()->id() !== $post->user_id && ! auth()->user()->isAdmin())) {
                abort(404);
            }
        }

        // Chống spam lượt xem: chỉ tăng view cho bài đã xuất bản và 1 IP chỉ tính 1 lần trong 1 giờ
        if ($post->status === 'published') {
            $ip = $request->ip();
            $cacheKey = 'viewed_post_'.$post->id.'_'.$ip;

            if (! Cache::has($cacheKey)) {
                $post->increment('views');
                Cache::put($cacheKey, true, now()->addHours(1));
            }
        }

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return view('frontend.detail', compact('post', 'relatedPosts'));
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Bình luận của bạn đã được đăng tải.');
    }
}
