<?php

namespace App\Listeners\Task;

use App\Events\Task\TaskUpdateEvent;
use App\Models\User;
use App\Notifications\Task\TaskUpdateNotifications as TaskTaskUpdateNotifications;

class TaskUpdateNotifications
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

        $notifiables = collect();

        if ($task->user && $task->user->id !== $updatedBy->id) {
            $notifiables->push($task->user);
        }

        $projectManagers = User::role('project_manager')->get();
        foreach ($projectManagers as $manager) {
            if ($manager->id !== $updatedBy->id && $manager->id !== $task->assigned_to_user_id) {
                $notifiables->push($manager);
            }
        }

         $notifiables->unique('id')->each(function ($user) use ($task, $updatedBy) {
            $user->notify(new TaskTaskUpdateNotifications ($task, $updatedBy));
           
        });
    }
}
