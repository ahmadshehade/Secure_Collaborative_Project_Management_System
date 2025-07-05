<?php

namespace App\Observers;

use App\Models\Project;
use App\Traits\HasAttachments;

class ProjectObserver
{
    use HasAttachments;

    /**
     * Summary of deleting
     * @param \App\Models\Project $project
     * @return void
     */
    public function deleting(Project $project): void
    {
        $project->load([
            'attachments',
            'comments.attachments',
            'tasks.attachments',
            'tasks.comments.attachments',
        ]);


        $this->deleteAttachments($project);

        foreach ($project->comments as $comment) {
            $this->deleteAttachments($comment);
            $comment->delete();
        }

        foreach ($project->tasks as $task) {
            $task->delete();
        }
    }
}
