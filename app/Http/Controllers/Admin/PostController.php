<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'user'])->latest()->paginate(10);

        return view('admin.posts.index', compact('posts'));
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
            'status' => ['required', 'in:draft,published'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']).'-'.time();
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Đã thêm bài viết thành công!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:news,blog'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
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

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công!');
    }
}
