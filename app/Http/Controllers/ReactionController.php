<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use App\Notifications\NewReactionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    /**
     * Thả icon hoặc đổi/hủy cảm xúc trên bài viết
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập tài khoản để thả icon cảm xúc!',
                'auth_required' => true,
            ], 401);
        }

        $request->validate([
            'type' => ['required', 'string', 'in:like,love,haha,wow,sad,angry'],
        ]);

        $user = Auth::user();
        $userId = $user->id;
        $type = $request->input('type');

        $existing = PostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        $action = 'added';
        $userReaction = $type;

        if ($existing) {
            if ($existing->type === $type) {
                // Nhấp lại cùng 1 icon thì hủy cảm xúc
                $existing->delete();
                $action = 'removed';
                $userReaction = null;
            } else {
                // Đổi sang loại cảm xúc khác
                $existing->update(['type' => $type]);
                $action = 'updated';
            }
        } else {
            // Thả cảm xúc mới
            PostReaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'type'    => $type,
            ]);
        }

        // Gửi thông báo cho tác giả nếu được thả reaction mới
        if ($action !== 'removed' && $post->user_id !== $userId) {
            $post->user->notify(new NewReactionNotification($user, $post, $type));
        }

        $counts = $post->fresh()->getReactionCounts();

        return response()->json([
            'success'       => true,
            'action'        => $action,
            'user_reaction' => $userReaction,
            'counts'        => $counts,
        ]);
    }
}
