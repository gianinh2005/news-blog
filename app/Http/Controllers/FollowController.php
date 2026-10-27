<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollowerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Bật / tắt theo dõi một tác giả
     */
    public function toggle(Request $request, User $author): JsonResponse
    {
        $user = $request->user();

        if ($user->id === $author->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự theo dõi chính mình.',
            ], 400);
        }

        if ($user->isFollowing($author)) {
            $user->following()->detach($author->id);
            $isFollowing = false;
            $message = "Đã hủy theo dõi tác giả {$author->name}.";
        } else {
            $user->following()->attach($author->id);
            $isFollowing = true;
            $message = "Đã theo dõi tác giả {$author->name} thành công!";

            // Gửi thông báo cho tác giả
            $author->notify(new NewFollowerNotification($user));
        }

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'message' => $message,
            'followers_count' => $author->followers()->count(),
        ]);
    }
}
