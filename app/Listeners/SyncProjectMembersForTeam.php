<?php

namespace App\Listeners;

use App\Events\TeamMembersUpdated;

class SyncProjectMembersForTeam
{
    /**
     * Summary of handle
     * @param \App\Events\TeamMembersUpdated $event
     * @return void
     */
    public function handle(TeamMembersUpdated $event)
    {
        $team = $event->team;

        $memberIds = $team->members()->pluck('users.id')->toArray();

        foreach ($team->projects as $project) {
            $project->members()->sync($memberIds);
        }
    }
}
