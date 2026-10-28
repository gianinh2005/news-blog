<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Xuất danh sách bài viết ra file CSV chuẩn UTF-8 (mở bằng Excel không lỗi tiếng Việt)
     */
    public function exportPosts(): StreamedResponse
    {
        $fileName = 'danh-sach-bai-viet-'.date('Y-m-d').'.csv';
        $posts = Post::with(['user', 'category'])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($posts) {
            $handle = fopen('php://output', 'w');
            // Thêm UTF-8 BOM để Excel tự nhận diện Unicode
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Tiêu đề', 'Chuyên mục', 'Tác giả', 'Loại bài', 'Trạng thái', 'Lượt xem', 'Ngày tạo']);

            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->category?->name ?? 'N/A',
                    $post->user?->name ?? 'N/A',
                    $post->type === 'news' ? 'Tin tức' : 'Blog',
                    $post->status,
                    $post->views,
                    $post->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Xuất danh sách thành viên ra file CSV
     */
    public function exportUsers(): StreamedResponse
    {
        $fileName = 'danh-sach-thanh-vien-'.date('Y-m-d').'.csv';
        $users = User::withCount('publishedPosts')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Họ tên', 'Email', 'Vai trò', 'Trạng thái', 'Cấp độ', 'EXP', 'Số bài xuất bản', 'Ngày tham gia']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->is_active ? 'Hoạt động' : 'Bị khóa',
                    "Lv.{$user->level} ({$user->level_title})",
                    $user->exp,
                    $user->published_posts_count,
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Xuất danh sách báo cáo vi phạm ra file CSV
     */
    public function exportReports(): StreamedResponse
    {
        $fileName = 'bao-cao-vi-pham-'.date('Y-m-d').'.csv';
        $reports = Report::with(['user'])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Người báo cáo', 'Loại nội dung', 'ID Nội dung', 'Lý do', 'Trạng thái', 'Ghi chú xử lý', 'Ngày tạo']);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->id,
                    $report->user?->name ?? 'Khách',
                    $report->reportable_type,
                    $report->reportable_id,
                    $report->reason,
                    $report->status,
                    $report->note ?? '',
                    $report->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
