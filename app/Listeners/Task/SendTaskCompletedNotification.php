<?php

namespace App\Listeners\Task;

use App\Events\Task\TaskCompletedEvent;

use App\Notifications\Task\TaskCompletedNotification;


class SendTaskCompletedNotification
{
    
    /**
     * Summary of handle
     * @param \App\Events\Task\TaskCompletedEvent $event
     * @return void
     */
    public function handle(TaskCompletedEvent $event): void
    {
          $task = $event->task;
         $usersToNotify = $task->project->members
            ->push($task->project->userCreated)
            ->unique('id');
           

        foreach ($usersToNotify as $user) {
            $user->notify(new TaskCompletedNotification($task));
        }
    }



  
}
