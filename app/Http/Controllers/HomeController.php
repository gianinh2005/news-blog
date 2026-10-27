<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user', 'tags'])
            ->where('status', 'published')
            ->latest('published_at');

        // Tìm kiếm theo từ khóa
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('title', 'like', "%{$q}%");
        }

        // Lọc theo loại (news hoặc blog)
        if ($request->filled('type') && in_array($request->type, ['news', 'blog'])) {
            $query->where('type', $request->type);
        }

        // Lọc theo bài viết từ tác giả đang theo dõi
        if ($request->filled('filter') && $request->filter === 'following' && Auth::check()) {
            $followingAuthorIds = Auth::user()->following()->pluck('author_id');
            $query->whereIn('user_id', $followingAuthorIds);
        }

        $posts = $query->paginate(6)->appends($request->all());
        $featuredPosts = Post::where('status', 'published')->orderBy('views', 'desc')->take(5)->get();
        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return view('frontend.index', compact('posts', 'featuredPosts', 'categories', 'popularTags'));
    }

    public function showCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()
            ->with(['user', 'tags'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(6);

        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return view('frontend.category', compact('category', 'posts', 'categories', 'popularTags'));
    }

    public function showTag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()
            ->with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(6);

        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return view('frontend.tag', compact('tag', 'posts', 'categories', 'popularTags'));
    }

    public function showPost(Request $request, string $slug)
    {
        $post = Post::with([
            'category', 
            'user.followers', 
            'comments.user', 
            'comments.replies.user', 
            'reactions', 
            'tags'
        ])
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

        $reactionCounts = $post->getReactionCounts();
        $userReaction = $post->userReaction(Auth::id());
        $isSaved = $post->isSavedBy(Auth::user());
        $isFollowingAuthor = Auth::check() ? Auth::user()->isFollowing($post->user) : false;

        return view('frontend.detail', compact(
            'post', 
            'relatedPosts', 
            'reactionCounts', 
            'userReaction',
            'isSaved',
            'isFollowingAuthor'
        ));
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
            'parent_id' => ['nullable', 'exists:comments,id'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min'      => 'Bình luận phải có ít nhất 2 ký tự.',
            'content.max'      => 'Bình luận không được vượt quá 1000 ký tự.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $comment = $post->allComments()->create([
            'user_id'   => $user->id,
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        // Gửi thông báo cho tác giả bài viết
        if ($post->user_id !== $user->id && ! $request->parent_id) {
            $post->user->notify(new NewCommentNotification($user, $comment, false));
        }

        // Gửi thông báo cho người được trả lời
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment && $parentComment->user_id !== $user->id) {
                $parentComment->user->notify(new NewCommentNotification($user, $comment, true));
            }
        }

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

        if ($comment->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
