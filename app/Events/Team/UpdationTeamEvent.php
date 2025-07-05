<?php

namespace App\Events\Team;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\User;

class UpdationTeamEvent
{
    use Dispatchable, SerializesModels;

    public $team;
    public $updatedBy;

    public function __construct(Team $team, User $updatedBy)
    {
        $this->team = $team;
        $this->updatedBy = $updatedBy;
    }
}

