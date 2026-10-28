<?php

namespace App\Models;

use Database\Factories\AdminLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class AdminLog extends Model
{
    /** @use HasFactory<AdminLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'action_type',
        'description',
        'subject_id',
        'subject_type',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public const ACTION_TYPES = [
        'all' => 'Tất cả loại thao tác',
        'post' => 'Bài viết',
        'comment' => 'Bình luận',
        'user' => 'Tài khoản',
        'category' => 'Danh mục',
        'report' => 'Báo cáo vi phạm',
        'subscriber' => 'Bản tin Email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Ghi nhật ký thao tác của Admin
     */
    public static function record(
        string $action,
        string $description,
        string $actionType = 'general',
        ?Model $subject = null,
        ?array $properties = null,
        ?int $userId = null
    ): self {
        $user = Auth::user();

        return static::create([
            'user_id' => $userId ?? $user?->id,
            'action' => $action,
            'action_type' => $actionType,
            'description' => $description,
            'subject_id' => $subject?->getKey(),
            'subject_type' => $subject ? get_class($subject) : null,
            'properties' => $properties,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Lấy cấu hình badge hiển thị theo hành động
     */
    public function getBadge(): array
    {
        return match ($this->action) {
            'approve_post' => [
                'label' => 'Duyệt bài',
                'class' => 'bg-success text-white',
                'icon' => 'bi-check-circle-fill',
            ],
            'reject_post' => [
                'label' => 'Từ chối bài',
                'class' => 'bg-warning text-dark',
                'icon' => 'bi-x-circle-fill',
            ],
            'create_post' => [
                'label' => 'Tạo bài viết',
                'class' => 'bg-primary text-white',
                'icon' => 'bi-file-earmark-plus-fill',
            ],
            'update_post' => [
                'label' => 'Sửa bài viết',
                'class' => 'bg-info text-dark',
                'icon' => 'bi-pencil-square',
            ],
            'delete_post' => [
                'label' => 'Xóa bài viết',
                'class' => 'bg-danger text-white',
                'icon' => 'bi-trash-fill',
            ],
            'delete_comment' => [
                'label' => 'Xóa bình luận',
                'class' => 'bg-danger text-white',
                'icon' => 'bi-chat-left-x-fill',
            ],
            'update_role' => [
                'label' => 'Đổi vai trò',
                'class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon' => 'bi-person-gear',
            ],
            'toggle_user_status' => [
                'label' => 'Khóa/Mở tài khoản',
                'class' => 'bg-secondary text-white',
                'icon' => 'bi-shield-lock-fill',
            ],
            'create_category' => [
                'label' => 'Tạo danh mục',
                'class' => 'bg-success text-white',
                'icon' => 'bi-folder-plus',
            ],
            'update_category' => [
                'label' => 'Sửa danh mục',
                'class' => 'bg-info text-dark',
                'icon' => 'bi-folder-symlink-fill',
            ],
            'delete_category' => [
                'label' => 'Xóa danh mục',
                'class' => 'bg-danger text-white',
                'icon' => 'bi-folder-minus',
            ],
            'dismiss_report' => [
                'label' => 'Bỏ qua báo cáo',
                'class' => 'bg-secondary text-white',
                'icon' => 'bi-shield-check',
            ],
            'take_down_content' => [
                'label' => 'Gỡ vi phạm',
                'class' => 'bg-danger text-white',
                'icon' => 'bi-shield-slash-fill',
            ],
            'lock_user_report' => [
                'label' => 'Khóa người vi phạm',
                'class' => 'bg-dark text-white',
                'icon' => 'bi-person-x-fill',
            ],
            'delete_subscriber' => [
                'label' => 'Xóa subscriber',
                'class' => 'bg-secondary text-white',
                'icon' => 'bi-envelope-x-fill',
            ],
            default => [
                'label' => $this->action,
                'class' => 'bg-light text-dark border',
                'icon' => 'bi-activity',
            ],
        };
    }
}
