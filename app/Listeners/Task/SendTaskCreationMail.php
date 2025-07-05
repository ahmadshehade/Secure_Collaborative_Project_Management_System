<?php

namespace App\Listeners\Task;

use App\Events\Task\MakeTaskEvent;
use App\Jobs\Task\MakeTaskJob;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCreationMail
{

  
    /**
     * Summary of handle
     * @param \App\Events\Task\MakeTaskEvent $event
     * @return void
     */
    public function handle(MakeTaskEvent $event): void
    {
        $task = $event->task;
        $createdBy = $event->createdBy;

        $recipients = collect();

        if ($task->user && $task->user->id !== $createdBy->id) {
            $recipients->push($task->user);
       
        }
      dispatch(new MakeTaskJob($task, $createdBy, $recipients));
    }
}
