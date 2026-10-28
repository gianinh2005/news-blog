<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Category;
use App\Models\Post;
use App\Notifications\PostStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Admin chỉ xem được:
        // 1. Bài của chính Admin (bất kể trạng thái nào)
        // 2. Bài của người dùng khác NHƯNG chỉ khi bài đó đã được gửi duyệt (pending, published, rejected), KHÔNG xem bản nháp riêng tư của user
        $query = Post::with(['category', 'user'])
            ->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('status', '!=', 'draft');
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(10)->withQueryString();

        $countPending = Post::where('status', 'pending')->count();

        return view('admin.posts.index', compact('posts', 'countPending'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:news,blog'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,pending,rejected'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']).'-'.time();
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        $newPost = Post::create($data);
        $newPost->user?->syncExpAndLevel();

        AdminLog::record('create_post', "Tạo bài viết mới: '{$newPost->title}'", 'post', $newPost);

        return redirect()->route('admin.posts.index')->with('success', 'Đã thêm bài viết thành công!');
    }

    public function edit(Post $post)
    {
        // Admin chỉ được sửa nội dung bài viết của chính Admin tạo
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn chỉ có quyền Duyệt hoặc Từ chối bài viết của người khác, không được tự ý sửa nội dung tác giả.');
        }

        $categories = Category::all();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn chỉ có quyền Duyệt hoặc Từ chối bài viết của người khác, không được tự ý sửa nội dung tác giả.');
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:news,blog'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,pending,rejected'],
            'reject_reason' => ['nullable', 'string', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($post->status !== 'published' && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        $post->update($data);
        $post->user?->syncExpAndLevel();

        AdminLog::record('update_post', "Cập nhật bài viết: '{$post->title}'", 'post', $post);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Duyệt bài viết nhanh (Phê duyệt).
     */
    public function approve(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
            'reject_reason' => null,
        ]);

        // Thưởng EXP và tăng cấp cho người viết bài
        $post->user?->syncExpAndLevel();

        // Gửi thông báo phê duyệt tới tác giả
        $post->user?->notify(new PostStatusNotification($post, 'published'));

        AdminLog::record('approve_post', "Phê duyệt và xuất bản bài viết: '{$post->title}'", 'post', $post);

        return back()->with('success', "Đã phê duyệt và xuất bản bài viết '{$post->title}'! Tác giả nhận được +100 EXP.");
    }

    /**
     * Từ chối bài viết kèm lý do.
     */
    public function reject(Request $request, Post $post)
    {
        $request->validate([
            'reject_reason' => ['required', 'string', 'max:500'],
        ]);

        $post->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        $post->user?->syncExpAndLevel();

        // Gửi thông báo từ chối tới tác giả
        $post->user?->notify(new PostStatusNotification($post, 'rejected', $request->reject_reason));

        AdminLog::record(
            'reject_post',
            "Từ chối bài viết: '{$post->title}' - Lý do: {$request->reject_reason}",
            'post',
            $post,
            ['reject_reason' => $request->reject_reason]
        );

        return back()->with('success', "Đã từ chối bài viết '{$post->title}'!");
    }

    public function destroy(Post $post)
    {
        $author = $post->user;
        $title = $post->title;

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        $author?->syncExpAndLevel();

        AdminLog::record('delete_post', "Xóa bài viết: '{$title}'", 'post');

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công!');
    }
}
