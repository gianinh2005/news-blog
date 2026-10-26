<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Tiếp nhận báo cáo vi phạm từ người dùng.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string', Rule::in(['post', 'comment'])],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'string', Rule::in(array_keys(Report::REASONS))],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $modelClass = $validated['reportable_type'] === 'post' ? Post::class : Comment::class;
        $target = $modelClass::findOrFail($validated['reportable_id']);

        // Kiểm tra xem user này đã gửi báo cáo cho nội dung này trước đó và đang chờ xử lý hay không
        $existing = Report::where('user_id', auth()->id())
            ->where('reportable_type', $modelClass)
            ->where('reportable_id', $target->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Bạn đã gửi báo cáo cho nội dung này rồi. Ban quản trị đang xử lý!');
        }

        Report::create([
            'user_id' => auth()->id(),
            'reportable_type' => $modelClass,
            'reportable_id' => $target->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Cảm ơn bạn! Báo cáo vi phạm đã được gửi đến ban quản trị để xem xét.');
    }
}
