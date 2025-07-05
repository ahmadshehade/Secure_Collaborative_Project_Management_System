<?php

namespace App\Notifications\Projects;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $data;
    public $deletedBy;
    /**
     * Summary of __construct
     * @param mixed $project
     * @param mixed $deletedBy
     */
    public function __construct($project, $deletedBy)
    {
        $this->data = [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'created_by' => $project->userCreated->name,
            'team_id' => $project->team_id,
        ];
        $this->deletedBy = $deletedBy;
    }

    /**
     * Summary of via
     * @param object $notifiable
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Summary of toArray
     * @param object $notifiable
     * @return array{created_by: mixed, deleted_by: mixed, message: string, project_id: mixed, project_name: mixed, team_id: mixed}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->data['project_id'],
            'project_name' => $this->data['project_name'],
            'created_by' => $this->data['created_by'],
            'message' => 'Successfully deleted project ' . $this->data['project_name'],
            'team_id' => $this->data['team_id'],
            'deleted_by' => $this->deletedBy,
        ];
    }
}
