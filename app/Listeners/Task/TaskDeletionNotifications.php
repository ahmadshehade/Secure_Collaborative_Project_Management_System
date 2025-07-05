<?php

namespace App\Listeners\Task;

use App\Events\Task\DeleteTaskEvent;
use App\Models\User;
use App\Notifications\Task\TaskDeleteNotifications ;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskDeletionNotifications
{
 
    /**
     * Summary of handle
     * @param \App\Events\Task\DeleteTaskEvent $event
     * @return void
     */
    public function handle(DeleteTaskEvent $event): void
     {
        $task = $event->task;
        $deletedBy = $event->deletedBy;

        $assignedUser = $task->user;
        $project = $task->project;
        $projectManagers = User::role('project_manager')->get();
        $teamMembers = $project->members;

        $notifiables = collect()
            ->push($assignedUser)
            ->merge($projectManagers)
            ->merge($teamMembers)
            ->unique('id')
            ->filter(fn($user) => $user->id !== $deletedBy->id);

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new TaskDeleteNotifications($task, $deletedBy));
        }
    }
}
