<?php

namespace App\Notifications\Team;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamDeleteNotifications extends Notification implements ShouldQueue
{
    use Queueable;
    public $teamData;
    public $deletedBy;

    /**
     * Summary of __construct
     * @param array $teamData
     * @param mixed $deletedBy
     */
    public function __construct(array $teamData, $deletedBy)
    {
        $this->teamData = $teamData;
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
     * @return array{deletedBy: mixed, team_id: mixed, team_name: mixed, team_owner: mixed}
     */
    public function toArray(object $notifiable): array
    {
        return [
            "team_id" => $this->teamData['id'],
            "team_name" => $this->teamData['name'],
            "team_owner" => $this->teamData['owner_name'],
            "deletedBy" => $this->deletedBy->name,
        ];
    }
}


