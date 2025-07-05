<?php

namespace App\Listeners\Project;

use App\Events\Project\ProjectUpdationEvent;
use App\Models\User;
use App\Notifications\Projects\ProjectUpdateNotification;

class ProjectUpdationListener
{
    /**
     * Summary of handle
     * @param \App\Events\Project\ProjectUpdationEvent $event
     * @return void
     */
    public function handle(ProjectUpdationEvent $event): void
    {
        $project = $event->project;
        $updatedBy = $event->updatedBy;
        $team = $project->team;

        $notifiables = collect()
            ->merge(User::role('project_manager')->get())
            ->push($team->owner)
            ->merge($team->members)
            ->unique('id')
            ->filter(fn($user) => $user->id !== $updatedBy->id);

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ProjectUpdateNotification($project, $updatedBy));
        }
    }
}

