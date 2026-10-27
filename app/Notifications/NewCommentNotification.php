<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $commenter,
        public Comment $comment,
        public bool $isReply = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $post = $this->comment->post;
        $title = $this->isReply 
            ? "{$this->commenter->name} đã trả lời bình luận của bạn."
            : "{$this->commenter->name} đã bình luận bài viết của bạn.";

        return [
            'type'        => 'comment',
            'title'       => $title,
            'message'     => \Illuminate\Support\Str::limit($this->comment->content, 80),
            'url'         => route('post.show', $post->slug) . '#comment-' . $this->comment->id,
            'actor_name'  => $this->commenter->name,
            'actor_avatar'=> $this->commenter->avatar,
            'post_title'  => $post->title,
        ];
    }
}
