<?php

namespace App\Notifications\Projects;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ProjectUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $project;
    public $updatedBy;
    /**
     * Create a new notification instance.
     */
    public function __construct($project, $updatedBy)
    {
        $this->project = $project;
        $this->updatedBy = $updatedBy;
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
        try {
            return [
                'project_id' => $this->project->id,
                'project_name' => $this->project->name,
                'satus' => $this->project->status,
                'created_by' => $this->project->userCreated->name,
                'message' => 'Successfully Update Poject ' . $this->project->name,
                'team_id' => $this->project->team_id,
                'updated_by' => $this->updatedBy->name ?? 'Unknown User',
            ];
        } catch (\Exception $e) {
            Log::error("Fail To Send Notification" . $e->getMessage());
            return [];
        }
    }
}
