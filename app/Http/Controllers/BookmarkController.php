<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Bật / tắt lưu bài viết yêu thích
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        if ($user->savedPosts()->where('post_id', $post->id)->exists()) {
            $user->savedPosts()->detach($post->id);
            $isSaved = false;
            $message = 'Đã bỏ lưu bài viết khỏi mục yêu thích.';
        } else {
            $user->savedPosts()->attach($post->id);
            $isSaved = true;
            $message = 'Đã lưu bài viết vào danh sách yêu thích thành công!';
        }

        return response()->json([
            'success' => true,
            'is_saved' => $isSaved,
            'message' => $message,
            'total_saved' => $user->savedPosts()->count(),
        ]);
    }
}
