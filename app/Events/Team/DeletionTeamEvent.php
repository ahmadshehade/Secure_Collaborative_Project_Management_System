<?php

namespace App\Events\Team;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletionTeamEvent
{
    use Dispatchable, SerializesModels;

    public $teamData;
    public $deletedBy;

    public function __construct(array $teamData, $deletedBy)
    {
        $this->teamData = $teamData;
        $this->deletedBy = $deletedBy;
    }
}

