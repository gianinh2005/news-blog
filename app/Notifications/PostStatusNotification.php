<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Post $post,
        public string $status,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'published';
        $title = $isApproved
            ? "Bài viết của bạn đã được Admin phê duyệt!"
            : "Bài viết của bạn đã bị từ chối xuất bản.";

        $message = $isApproved
            ? "Bài viết \"{$this->post->title}\" đã được xuất bản và bạn nhận được +100 EXP!"
            : ($this->reason ? "Lý do: {$this->reason}" : "Bài viết \"{$this->post->title}\" chưa đạt tiêu chuẩn kiểm duyệt.");

        return [
            'type'        => $isApproved ? 'post_approved' : 'post_rejected',
            'title'       => $title,
            'message'     => $message,
            'url'         => $isApproved ? route('post.show', $this->post->slug) : route('user.posts.index'),
            'post_title'  => $this->post->title,
        ];
    }
}
