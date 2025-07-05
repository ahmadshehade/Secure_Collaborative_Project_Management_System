<?php

namespace App\Events\Project;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class ProjectDeletionEvent
{
    use Dispatchable, SerializesModels;

    public $project;
    public $deletedBy;

    /**
     * Summary of __construct
     * @param \App\Models\Project $project
     * @param \App\Models\User $deletedBy
     */
    public function __construct(Project $project, User $deletedBy)
    {
        $this->project = $project;
        $this->deletedBy = $deletedBy;
    }
}

