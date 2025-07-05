<?php

namespace App\Listeners\Project;

use App\Events\Project\ProjectDeletionEvent;
use App\Models\User;
use App\Notifications\Projects\ProjectDeleteNotification;

class ProjectDeletionListener
{
    /**
     * Summary of handle
     * @param \App\Events\Project\ProjectDeletionEvent $event
     * @return void
     */
    public function handle(ProjectDeletionEvent $event): void
    {
        $project = $event->project;
        $deletedByUser = $event->deletedBy;
        
        $projectManagers = User::role('project_manager')->get();
        $teamOwner = $project->team->owner;
        $teamMembers = $project->team->members;

        $notifiables = collect()
            ->merge($projectManagers)
            ->push($teamOwner)
            ->merge($teamMembers)
            ->unique('id')
            ->filter(fn($user) => $user->id !== $deletedByUser->id);

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ProjectDeleteNotification($project, $deletedByUser->name));
        }
    }
}

