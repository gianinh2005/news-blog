<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPostController extends Controller
{
    /**
     * Danh sách bài viết do chính user hiện tại đã đăng.
     */
    public function index()
    {
        $posts = Post::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.posts.index', compact('posts'));
    }

    /**
     * Giao diện form viết bài blog mới.
     */
    public function create()
    {
        $categories = Category::all();

        return view('user.posts.create', compact('categories'));
    }

    /**
     * Lưu bài viết mới do user đăng.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['user_id'] = auth()->id();
        $data['type'] = 'blog'; // User đăng mặc định là dạng Blog cá nhân
        $data['slug'] = Str::slug($data['title']).'-'.time();
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('user.posts.index')->with('success', 'Đăng bài blog thành công!');
    }

    /**
     * Giao diện chỉnh sửa bài viết của chính user.
     */
    public function edit(Post $post)
    {
        // Đảm bảo chỉ sửa được bài viết của chính mình
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        $categories = Category::all();

        return view('user.posts.edit', compact('post', 'categories'));
    }

    /**
     * Cập nhật bài viết của user.
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($data['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail && Storage::disk('public')->exists($post->thumbnail)) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('user.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Xóa bài viết của user.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa bài viết này.');
        }

        if ($post->thumbnail && Storage::disk('public')->exists($post->thumbnail)) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();

        return redirect()->route('user.posts.index')->with('success', 'Đã xóa bài viết thành công!');
    }
}
