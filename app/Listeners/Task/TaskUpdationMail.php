<?php

namespace App\Listeners\Task;

use App\Events\Task\TaskUpdateEvent;
use App\Jobs\Task\TaskUpdateJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskUpdationMail
{
  

    /**
     * Summary of handle
     * @param \App\Events\Task\TaskUpdateEvent $event
     * @return void
     */
    public function handle(TaskUpdateEvent $event): void
    {
        $task = $event->task;
        $updatedBy = $event->updatedBy;
       

          $recipients = collect();

        if ($task->user && $task->user->id !== $updatedBy->id) {
            $recipients->push($task->user);
        }
           dispatch(new TaskUpdateJob($task, $updatedBy, $recipients));

    }
}
