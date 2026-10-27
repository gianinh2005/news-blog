<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'type',
    ];

    public const TYPES = [
        'like'  => ['label' => 'Thích', 'icon' => '👍', 'color' => '#3b82f6'],
        'love'  => ['label' => 'Yêu thích', 'icon' => '❤️', 'color' => '#ef4444'],
        'haha'  => ['label' => 'Haha', 'icon' => '😂', 'color' => '#f59e0b'],
        'wow'   => ['label' => 'Wow', 'icon' => '😮', 'color' => '#10b981'],
        'sad'   => ['label' => 'Buồn', 'icon' => '😢', 'color' => '#6366f1'],
        'angry' => ['label' => 'Phẫn nộ', 'icon' => '😡', 'color' => '#dc2626'],
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
