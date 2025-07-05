<?php

namespace App\Events;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMembersUpdated
{
    use Dispatchable, SerializesModels;

    public Team $team;

    /**
     * Summary of __construct
     * @param \App\Models\Team $team
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
