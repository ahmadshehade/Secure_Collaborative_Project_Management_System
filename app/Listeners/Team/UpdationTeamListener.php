<?php

namespace App\Listeners\Team;

use App\Events\Team\UpdationTeamEvent;
use App\Models\User;
use App\Notifications\Team\TeamUpdateNotifications;

class UpdationTeamListener
{
    /**
     * Summary of handle
     * @param \App\Events\Team\UpdationTeamEvent $event
     * @return void
     */
    public function handle(UpdationTeamEvent $event): void
    {
        $team = $event->team;
        $updatedBy = $event->updatedBy;

        $team->members->each(fn($user) =>
            $user->notify(new TeamUpdateNotifications($team, $updatedBy))
        );

        User::role('admin')->each(fn($admin) =>
            $admin->notify(new TeamUpdateNotifications($team, $updatedBy))
        );
    }
}
