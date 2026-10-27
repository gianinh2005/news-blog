<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReactionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public Post $post,
        public string $reactionType
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $emojiMap = [
            'like'  => '👍 Thích',
            'love'  => '❤️ Yêu thích',
            'haha'  => '😄 Haha',
            'wow'   => '😮 Wow',
            'sad'   => '😢 Buồn',
            'angry' => '😡 Phẫn nộ',
        ];

        $reactionText = $emojiMap[$this->reactionType] ?? 'thả cảm xúc';

        return [
            'type'        => 'reaction',
            'title'       => "{$this->user->name} đã {$reactionText} bài viết của bạn.",
            'message'     => \Illuminate\Support\Str::limit($this->post->title, 70),
            'url'         => route('post.show', $this->post->slug),
            'actor_name'  => $this->user->name,
            'actor_avatar'=> $this->user->avatar,
            'post_title'  => $this->post->title,
        ];
    }
}
