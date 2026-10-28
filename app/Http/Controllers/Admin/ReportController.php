<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * Danh sách báo cáo vi phạm.
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reportable'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $typeClass = $request->type === 'post' ? Post::class : ($request->type === 'comment' ? Comment::class : null);
            if ($typeClass) {
                $query->where('reportable_type', $typeClass);
            }
        }

        $reports = $query->paginate(15)->withQueryString();

        $countPending = Report::where('status', 'pending')->count();

        return view('admin.reports.index', compact('reports', 'countPending'));
    }

    /**
     * Bác bỏ báo cáo (không vi phạm).
     */
    public function dismiss(Request $request, Report $report)
    {
        $report->update([
            'status' => 'dismissed',
            'admin_note' => $request->input('admin_note', 'Báo cáo không vi phạm tiêu chuẩn cộng đồng.'),
        ]);

        AdminLog::record('dismiss_report', "Bác bỏ báo cáo vi phạm #{$report->id}", 'report', $report);

        return back()->with('success', 'Đã đánh dấu bác bỏ báo cáo này.');
    }

    /**
     * Gỡ bỏ nội dung bị báo cáo (Bài viết hoặc Bình luận).
     */
    public function takeDownContent(Request $request, Report $report)
    {
        $target = $report->reportable;

        if (! $target) {
            $report->update([
                'status' => 'resolved',
                'admin_note' => 'Nội dung này vốn đã không còn tồn tại.',
            ]);

            AdminLog::record('take_down_content', "Đóng báo cáo #{$report->id} (Nội dung không còn tồn tại)", 'report', $report);

            return back()->with('success', 'Nội dung mục tiêu không còn tồn tại. Đã đóng báo cáo.');
        }

        if ($target instanceof Post) {
            // Chuyển bài viết về trạng thái từ chối/gỡ xuống
            $reason = $request->input('admin_note', 'Bài viết bị gỡ do vi phạm tiêu chuẩn cộng đồng sau khi có báo cáo.');
            $target->update([
                'status' => 'rejected',
                'reject_reason' => $reason,
            ]);
            $msg = "Đã gỡ bài viết '{$target->title}' khỏi website.";
        } elseif ($target instanceof Comment) {
            // Xóa bình luận vi phạm
            $target->delete();
            $msg = 'Đã xóa bình luận vi phạm.';
        } else {
            $msg = 'Đã xử lý nội dung.';
        }

        $report->update([
            'status' => 'resolved',
            'admin_note' => $request->input('admin_note', 'Đã gỡ nội dung vi phạm theo báo cáo.'),
        ]);

        AdminLog::record('take_down_content', "Xử lý báo cáo #{$report->id}: {$msg}", 'report', $report);

        return back()->with('success', $msg);
    }

    /**
     * Khóa tài khoản của tác giả nội dung vi phạm.
     */
    public function lockUser(Request $request, Report $report)
    {
        $target = $report->reportable;

        if (! $target || ! $target->user) {
            return back()->with('error', 'Không tìm thấy thông tin tác giả của nội dung này.');
        }

        $author = $target->user;
        $admin = $request->user();

        // Không cho tự khóa chính mình
        if ($admin->id === $author->id) {
            throw ValidationException::withMessages([
                'user' => 'Bạn không thể tự khóa tài khoản của chính mình.',
            ]);
        }

        // Không cho khóa Admin đang hoạt động cuối cùng
        if ($author->role === 'admin' && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'Không thể khóa tài khoản Admin duy nhất.',
            ]);
        }

        $author->is_active = false;
        $author->save();

        // Đồng thời cập nhật trạng thái báo cáo đã xử lý
        $report->update([
            'status' => 'resolved',
            'admin_note' => $request->input('admin_note', "Đã tạm khóa tài khoản '{$author->name}' vi phạm."),
        ]);

        AdminLog::record(
            'lock_user_report',
            "Khóa tài khoản người dùng '{$author->name}' ({$author->email}) do vi phạm theo báo cáo #{$report->id}",
            'report',
            $report,
            ['user_id' => $author->id]
        );

        return back()->with('success', "Đã tạm khóa tài khoản của người dùng '{$author->name}'.");
    }
}
