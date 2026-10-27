<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'exp',
        'level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'exp' => 'integer',
            'level' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(Post::class)->where('status', 'published');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'saved_posts')->withTimestamps()->latest('saved_posts.created_at');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'author_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'author_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User|int $author): bool
    {
        $authorId = $author instanceof User ? $author->id : $author;
        return $this->following()->where('author_id', $authorId)->exists();
    }

    /**
     * Danh hiệu tương ứng với Level
     */
    public function getLevelTitleAttribute(): string
    {
        return match (true) {
            $this->level >= 6 => 'Đại thần sáng tác',
            $this->level === 5 => 'Cây bút vàng',
            $this->level === 4 => 'Tác giả ưu tú',
            $this->level === 3 => 'Cây bút tích cực',
            $this->level === 2 => 'Cây bút tập sự',
            default => 'Tân thủ',
        };
    }

    /**
     * Màu sắc huy hiệu Level
     */
    public function getLevelBadgeColorAttribute(): string
    {
        return match (true) {
            $this->level >= 6 => '#7c3aed',
            $this->level === 5 => '#d97706',
            $this->level === 4 => '#2563eb',
            $this->level === 3 => '#059669',
            $this->level === 2 => '#0284c7',
            default => '#64748b',
        };
    }

    /**
     * Mốc điểm EXP cho cấp độ kế tiếp
     */
    public function getNextLevelExpAttribute(): int
    {
        return match (true) {
            $this->level >= 6 => 3000,
            $this->level === 5 => 2000,
            $this->level === 4 => 1000,
            $this->level === 3 => 600,
            $this->level === 2 => 300,
            default => 100,
        };
    }

    /**
     * Phần trăm tiến độ EXP tới cấp tiếp theo (0 - 100%)
     */
    public function getExpProgressAttribute(): int
    {
        $currentExp = $this->exp ?? 0;
        $nextExp = $this->next_level_exp;
        
        $prevLevelExp = match (true) {
            $this->level >= 6 => 2000,
            $this->level === 5 => 1000,
            $this->level === 4 => 600,
            $this->level === 3 => 300,
            $this->level === 2 => 100,
            default => 0,
        };

        if ($nextExp <= $prevLevelExp) {
            return 100;
        }

        $progress = (($currentExp - $prevLevelExp) / ($nextExp - $prevLevelExp)) * 100;
        return (int) max(0, min(100, round($progress)));
    }

    /**
     * Đồng bộ điểm kinh nghiệm và cấp bậc dựa trên số bài viết đã xuất bản
     * (Mỗi bài viết được xuất bản thưởng 100 EXP)
     */
    public function syncExpAndLevel(): void
    {
        $publishedCount = $this->publishedPosts()->count();
        $calculatedExp = $publishedCount * 100;

        $newLevel = match (true) {
            $calculatedExp >= 2000 => 6,
            $calculatedExp >= 1000 => 5,
            $calculatedExp >= 600 => 4,
            $calculatedExp >= 300 => 3,
            $calculatedExp >= 100 => 2,
            default => 1,
        };

        $this->update([
            'exp' => $calculatedExp,
            'level' => $newLevel,
        ]);
    }

    /**
     * Tổng lượt xem nhận được từ tất cả bài viết của user
     */
    public function getTotalViewsReceivedAttribute(): int
    {
        return (int) $this->publishedPosts()->sum('views');
    }

    /**
     * Tổng lượt thả cảm xúc nhận được từ tất cả bài viết của user
     */
    public function getTotalReactionsReceivedAttribute(): int
    {
        return (int) PostReaction::whereIn('post_id', $this->publishedPosts()->pluck('id'))->count();
    }

    /**
     * Tổng lượt bình luận nhận được từ tất cả bài viết của user
     */
    public function getTotalCommentsReceivedAttribute(): int
    {
        return (int) Comment::whereIn('post_id', $this->publishedPosts()->pluck('id'))->count();
    }

    /**
     * Tổng điểm tương tác: (Views * 1) + (Reactions * 5) + (Comments * 3)
     */
    public function getInteractionScoreAttribute(): int
    {
        return ($this->total_views_received * 1) 
            + ($this->total_reactions_received * 5) 
            + ($this->total_comments_received * 3);
    }
}