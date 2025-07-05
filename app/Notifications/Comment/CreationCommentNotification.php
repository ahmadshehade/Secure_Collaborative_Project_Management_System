<?php

namespace App\Notifications\Comment;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreationCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $createdBy;
    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $createdBy)
    {
        $this->comment = $comment;
        $this->createdBy = $createdBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $type = class_basename($this->comment->commentable_type);
        $title = method_exists($this->comment->commentable, 'name')
            ? $this->comment->commentable->name
            : ($this->comment->commentable->name ?? '');
        return [
            'content' => $this->comment->content,
            'commented_on' => $type . ': ' . $title,
            'created_by' => $this->createdBy->name,
            'commentable_id' => $this->comment->commentable_id,
            'commentable_type' => $type,
        ];
    }
}
