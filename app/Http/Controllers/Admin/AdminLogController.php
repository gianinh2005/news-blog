<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    /**
     * Hiển thị danh sách nhật ký thao tác của Admin.
     */
    public function index(Request $request): View
    {
        $query = AdminLog::with('user')->latest();

        // Lọc theo loại thao tác
        if ($actionType = $request->input('action_type')) {
            if ($actionType !== 'all') {
                $query->where('action_type', $actionType);
            }
        }

        // Lọc theo người thực hiện
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Tìm kiếm theo từ khóa
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo ngày
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => AdminLog::count(),
            'today' => AdminLog::whereDate('created_at', today())->count(),
            'posts' => AdminLog::where('action_type', 'post')->count(),
            'comments' => AdminLog::where('action_type', 'comment')->count(),
            'users' => AdminLog::where('action_type', 'user')->count(),
        ];

        $admins = User::where('role', 'admin')->get(['id', 'name', 'email']);
        $actionTypes = AdminLog::ACTION_TYPES;

        return view('admin.logs.index', compact('logs', 'stats', 'admins', 'actionTypes'));
    }

    /**
     * Dọn dẹp nhật ký (chỉ tài khoản admin@gmail.com có quyền).
     */
    public function clear(Request $request): RedirectResponse
    {
        if ($request->user()?->email !== 'admin@gmail.com') {
            abort(403, 'Chỉ tài khoản admin@gmail.com mới có quyền dọn dẹp nhật ký hoạt động.');
        }

        $days = (int) $request->input('days', 0);
        if ($days > 0) {
            AdminLog::where('created_at', '<', now()->subDays($days))->delete();
            $msg = "Đã dọn dẹp các bản ghi nhật ký cũ hơn {$days} ngày.";
        } else {
            AdminLog::truncate();
            $msg = 'Đã làm trống toàn bộ nhật ký thao tác.';
        }

        AdminLog::record(
            action: 'clear_logs',
            description: $msg,
            actionType: 'general'
        );

        return back()->with('success', $msg);
    }
}
