<?php

namespace App\Notifications\Team;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamCreateNotifications extends Notification implements ShouldQueue
{
    use Queueable;
     public $team;
     public $createdBy;
    /**
     * Create a new notification instance.
     */
    public function __construct(  $team,$createdBy)
    {
        $this->team = $team;
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
        "team_id"    => $this->team->id,
        "team_name"  => $this->team->name,
        "team_owner" => optional($this->team->owner)->name ?? 'N/A',
        "created_at" => optional($this->team->created_at)->format('Y-m-d H:i:s'),
        "createdBy"  => $this->createdBy->name ?? 'Unknown',
        ];
    }
}
