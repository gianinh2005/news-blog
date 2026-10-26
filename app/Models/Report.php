<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'admin_note',
    ];

    /**
     * Danh sách lý do báo cáo chuẩn cho select box.
     */
    public const REASONS = [
        'spam' => 'Nội dung rác / Spam / Quảng cáo',
        'hate_speech' => 'Ngôn từ kích động thù địch / Xúc phạm cá nhân',
        'false_info' => 'Thông tin sai sự thật / Tin giả',
        'violence' => 'Bạo lực / Khiêu dâm / Đồi trụy',
        'copyright' => 'Vi phạm bản quyền / Đạo văn',
        'other' => 'Lý do khác',
    ];

    /**
     * Người dùng gửi báo cáo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Đối tượng bị báo cáo (Post hoặc Comment).
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Lấy tên lý do tiếng Việt.
     */
    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
