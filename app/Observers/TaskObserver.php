<?php

namespace App\Observers;

use App\Events\Task\TaskCompletedEvent;
use App\Models\Task;
use App\Traits\HasAttachments;

class TaskObserver
{
    use HasAttachments;

    /**
     * Summary of creating
     * @param \App\Models\Task $task
     * @return void
     */
    public function creating(Task $task): void
    {
        if (empty($task->status)) {
            $task->status = 'pending';
        }
    }

    /**
     * Summary of updated
     * @param \App\Models\Task $task
     * @return void
     */
    public function updated(Task $task): void
    {
        if ($task->isDirty('status') && $task->status === 'completed') {
            event(new TaskCompletedEvent($task));
        }
    }

    /**
     * Summary of deleting
     * @param \App\Models\Task $task
     * @return void
     */
    public function deleting(Task $task): void
    {
        $task->load('attachments', 'comments.attachments');

        $this->deleteAttachments($task);

        foreach ($task->comments as $comment) {
            $this->deleteAttachments($comment);
            $comment->delete();
        }
    }
}
