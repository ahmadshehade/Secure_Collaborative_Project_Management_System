<?php

namespace App\Events\Project;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class ProjectUpdationEvent
{
    use Dispatchable, SerializesModels;

    public $project;
    public $updatedBy;

    /**
     * Summary of __construct
     * @param \App\Models\Project $project
     * @param \App\Models\User $updatedBy
     */
    public function __construct(Project $project, User $updatedBy)
    {
        $this->project = $project;
        $this->updatedBy = $updatedBy;
    }
}
