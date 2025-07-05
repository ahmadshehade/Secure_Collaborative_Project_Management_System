<?php

namespace App\Observers;

use App\Models\Team;

class TeamObserver
{
    /**
     * Summary of deleting
     * @param \App\Models\Team $team
     * @return void
     */
    public function deleting(Team $team): void
    {
        $team->load('projects');

        foreach ($team->projects as $project) {
            $project->delete();
        }
    }
}

