<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollowerNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $follower
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'follow',
            'title'       => "{$this->follower->name} đã bắt đầu theo dõi bạn.",
            'message'     => "Cộng đồng của bạn vừa có thêm 1 người theo dõi mới!",
            'url'         => route('members.index', ['tab' => 'posts', 'q' => $this->follower->name]),
            'actor_name'  => $this->follower->name,
            'actor_avatar'=> $this->follower->avatar,
        ];
    }
}
