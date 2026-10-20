<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Lưu bình luận mới của người dùng đã đăng nhập.
     *
     * Validation:
     *   - content: bắt buộc, kiểu chuỗi, tối thiểu 2 ký tự, tối đa 1000 ký tự.
     */
    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        // Kiểm tra dữ liệu đầu vào trước khi lưu
        $request->validate([
            'content' => [
                'required',
                'string',
                'min:2',    // Tối thiểu 2 ký tự, tránh comment rỗng/ký tự đơn
                'max:1000', // Giới hạn 1000 ký tự, tránh spam dài
            ],
        ], [
            // Thông báo lỗi tiếng Việt
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min'      => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max'      => 'Bình luận không được vượt quá 1000 ký tự.',
        ]);

        // Tạo bình luận gắn với bài viết và người dùng hiện tại
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Bình luận của bạn đã được đăng tải.');
    }

    /**
     * Xóa bình luận của chính người dùng đang đăng nhập.
     *
     * Chỉ chủ sở hữu bình luận mới được xóa.
     * Dùng authorize() để đảm bảo quyền sở hữu.
     */
    public function destroyComment(Comment $comment): RedirectResponse
    {
        // Kiểm tra quyền: chỉ người tạo bình luận mới được xóa
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận của bạn.');
    }
}
