<?php

namespace App\Events\Project;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class ProjectCreationEvent
{
    use Dispatchable, SerializesModels;

    public $project;
    public $createdBy;

    /**
     * Summary of __construct
     * @param \App\Models\Project $project
     * @param \App\Models\User $createdBy
     */
    public function __construct(Project $project, User $createdBy)
    {
        $this->project = $project;
        $this->createdBy = $createdBy;
    }
}

