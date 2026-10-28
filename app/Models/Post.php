<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail',
        'type',
        'status',
        'reject_reason',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies')->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_posts')->withTimestamps();
    }

    public function isSavedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->savedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Lấy số lượng từng loại cảm xúc
     */
    public function getReactionCounts(): array
    {
        $counts = [
            'like' => 0,
            'love' => 0,
            'haha' => 0,
            'wow' => 0,
            'sad' => 0,
            'angry' => 0,
            'total' => 0,
        ];

        $rawCounts = $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        foreach ($rawCounts as $type => $count) {
            if (isset($counts[$type])) {
                $counts[$type] = (int) $count;
            }
            $counts['total'] += (int) $count;
        }

        return $counts;
    }

    /**
     * Lấy reaction của một user trên bài viết này
     */
    public function userReaction(?int $userId): ?string
    {
        if (! $userId) {
            return null;
        }

        return $this->reactions()->where('user_id', $userId)->value('type');
    }
}
