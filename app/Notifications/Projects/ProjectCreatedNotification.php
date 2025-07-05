<?php

namespace App\Notifications\Projects;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ProjectCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;
    public $createdBy;
    /**
     * Create a new notification instance.
     */
    public function __construct($project, $createdBy)
    {
        $this->project = $project;
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
    public function toArray(object $notifiable)
    {
        try {
            return [
                'project_id' => $this->project->id,
                'project_name' => $this->project->name,
                'message' => 'Successfully Create Poject ' . $this->project->name,
                'team_id' => $this->project->team_id,
                'createdBy' => $this->createdBy->name,
                'satus' => $this->project->status,
            ];
        } catch (\Exception $e) {
            Log::error("Fail To Send Notification" . $e->getMessage());
            return [];
        }
    }
}
