<?php

namespace App\Notifications\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeleteNotifications extends Notification implements ShouldQueue
{
    use Queueable;
    public $task;
    public  $deletedBy ;
     protected array $data;

    /**
     * Create a new notification instance.
     */
 public function __construct($task, $deletedBy)
{
    $this->data = [
        'task_name' => $task->name,
        'project_name' => optional($task->project)->name,
        'assigned_to_user' => optional($task->user)->name,
        'deleted_by' => optional($deletedBy)->name,
    ];
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
      return $this->data;
    }
}
