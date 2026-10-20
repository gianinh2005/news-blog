<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CommentController (Admin)
 *
 * Quản lý bình luận từ phía admin:
 *  - index()   : Hiển thị danh sách toàn bộ bình luận, hỗ trợ tìm kiếm.
 *  - destroy() : Xóa bình luận spam / vi phạm nội quy.
 */
class CommentController extends Controller
{
    /**
     * Hiển thị danh sách tất cả bình luận.
     *
     * Admin có thể tìm kiếm theo từ khóa (nội dung, tên tác giả, tiêu đề bài viết).
     */
    public function index(Request $request): View
    {
        // Khởi tạo query lấy bình luận kèm thông tin user và bài viết
        $query = Comment::with(['user', 'post'])
            ->latest(); // Sắp xếp mới nhất lên đầu

        // Tìm kiếm theo từ khóa (nội dung bình luận, tên người dùng, tiêu đề bài viết)
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('content', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('post', fn ($p) => $p->where('title', 'like', "%{$keyword}%"));
            });
        }

        // Phân trang: 20 bình luận mỗi trang, giữ lại query string
        $comments = $query->paginate(20)->withQueryString();

        // Tổng số bình luận trong hệ thống (hiển thị trên thẻ stat)
        $totalComments = Comment::count();

        return view('admin.comments.index', compact('comments', 'totalComments'));
    }

    /**
     * Xóa một bình luận spam / vi phạm.
     *
     * Chỉ admin mới có quyền thực hiện hành động này (bảo vệ bởi middleware 'admin').
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận thành công.');
    }
}
