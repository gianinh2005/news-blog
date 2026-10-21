<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user'])
            ->where('status', 'published')
            ->latest('published_at');

        // Tìm kiếm theo tiêu đề
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('title', 'like', "%{$q}%");
        }

        // Lọc theo loại (news hoặc blog)
        if ($request->filled('type') && in_array($request->type, ['news', 'blog'])) {
            $query->where('type', $request->type);
        }

        $posts = $query->paginate(6)->appends($request->all());
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
            ->paginate(6);

        $categories = Category::withCount('posts')->get();

        return view('frontend.category', compact('category', 'posts', 'categories'));
    }

    public function showPost(Request $request, string $slug)
    {
        $post = Post::with(['category', 'user', 'comments.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Nếu bài chưa xuất bản, chỉ cho phép tác giả hoặc Admin xem trước
        if ($post->status !== 'published') {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (! $user || ($user->id !== $post->user_id && ! $user->isAdmin())) {
                abort(404);
            }
        }

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return view('frontend.detail', compact('post', 'relatedPosts'));
    }

    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'content' => [
                'required',
                'string',
                'min:2',
                'max:1000',
            ],
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min'      => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max'      => 'Bình luận không được vượt quá 1000 ký tự.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $post->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Bình luận của bạn đã được đăng tải.');
    }
    public function incrementViews(Post $post)
    {
        $post->increment('views');

        return response()->json([
            'success' => true,
            'views'   => number_format($post->views)
        ]);
    }

    public function destroyComment(Comment $comment): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận của bạn.');
    }
}
