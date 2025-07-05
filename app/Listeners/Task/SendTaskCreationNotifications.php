<?php

namespace App\Listeners\Task;

use App\Events\Task\MakeTaskEvent;
use App\Models\User;
use App\Notifications\Task\TaskCreateNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCreationNotifications
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

          if ($task->user && $task->user->id !== $createdBy->id) {
            $task->user->notify(new TaskCreateNotifications($task, $createdBy));
        }

        $projectManagers = User::role('project_manager')->get();
        foreach ($projectManagers as $manager) {
            if ($manager->id !== $createdBy->id && $manager->id !== $task->assigned_to_user_id) {
                $manager->notify(new TaskCreateNotifications($task, $createdBy));
            }
        }
    }
}
