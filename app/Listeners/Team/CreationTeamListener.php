<?php

namespace App\Listeners\Team;

use App\Events\Team\CreationTeamEvent;
use App\Models\User;
use App\Notifications\Team\TeamCreateNotifications;

class CreationTeamListener
{
    /**
     * Summary of handle
     * @param \App\Events\Team\CreationTeamEvent $event
     * @return void
     */
    public function handle(CreationTeamEvent $event): void
    {
        $team = $event->team;
        $createdBy = $event->createdBy;

        User::role('admin')->each(fn($admin) => 
            $admin->notify(new TeamCreateNotifications($team, $createdBy))
        );

        $team->members->each(function ($member) use ($team, $createdBy) {
            if ($member->id !== $createdBy->id) {
                $member->notify(new TeamCreateNotifications($team, $createdBy));
            }
        });
    }
}
