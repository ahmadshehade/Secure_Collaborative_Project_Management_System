<?php

namespace App\Notifications\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreateNotifications extends Notification implements ShouldQueue
{
    use Queueable;
    public $task;

    public $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($task,$createdBy)
    {
        $this->task = $task;
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
        return [
           'task_name'=>$this->task->name,
           'project_name'=>$this->task->project->name,
           'assigned_to_user'=>$this->task->user->name,
           'created_by'=>$this->createdBy->name,

        ];
    }
}
