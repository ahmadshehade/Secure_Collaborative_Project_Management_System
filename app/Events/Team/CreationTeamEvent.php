<?php

namespace App\Events\Team;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\User;

class CreationTeamEvent
{
    use Dispatchable, SerializesModels;

    public $team;
    public $createdBy;

    public function __construct(Team $team, User $createdBy)
    {
        $this->team = $team;
        $this->createdBy = $createdBy;
    }
}

