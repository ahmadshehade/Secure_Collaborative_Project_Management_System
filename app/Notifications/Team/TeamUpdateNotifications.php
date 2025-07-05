<?php

namespace App\Notifications\Team;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamUpdateNotifications extends Notification implements ShouldQueue
{
    use Queueable;
     public $team;
         public $updatedBy;
    /**
     * Create a new notification instance.
     */
    public function __construct( $team, $updatedBy )
    {
        $this->team = $team;
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
        return [
            "team_id"=>$this->team->id,
            "team_name"=>$this->team->name,
            "members"=>$this->team->members->pluck("name")->toArray(),
            "team_owner" => optional($this->team->owner)->name,
            "updatedBy"=>$this->updatedBy->name,

        ];
    }
}
