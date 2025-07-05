<?php

namespace App\Listeners;

use App\Events\ProjectTeamChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncProjectMembersOnTeamChange
{
    /**
     * Summary of handle
     * @param \App\Events\ProjectTeamChanged $event
     * @return void
     */
    public function handle(ProjectTeamChanged $event)
    {
        $team = $event->project->team;
        $memberIds = $team->members()->pluck('users.id')->toArray();
        $event->project->members()->sync($memberIds);
    }
}

