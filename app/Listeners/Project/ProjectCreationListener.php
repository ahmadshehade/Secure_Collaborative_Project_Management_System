<?php

namespace App\Listeners\Project;

use App\Events\Project\ProjectCreationEvent;
use App\Models\User;
use App\Notifications\Projects\ProjectCreatedNotification;

class ProjectCreationListener
{
    /**
     * Summary of handle
     * @param \App\Events\Project\ProjectCreationEvent $event
     * @return void
     */
    public function handle(ProjectCreationEvent $event): void
    {
        $project = $event->project;
        $createdBy = $event->createdBy;

        $team = $project->team;
        $teamOwner = $team->owner;
        $teamMembers = $team->members;
        $projectManagers = User::role('project_manager')->get();

        $notifiables = collect()
            ->merge($projectManagers)
            ->push($teamOwner)
            ->merge($teamMembers)
            ->unique('id')
            ->filter(fn($user) => $user->id !== $createdBy->id);

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ProjectCreatedNotification($project, $createdBy));
        }
    }
}

