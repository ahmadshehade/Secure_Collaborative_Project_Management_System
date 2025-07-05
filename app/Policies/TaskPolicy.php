<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Summary of before
     * @param \App\Models\User $user
     * 
     */
    public function before(User $user)
    {
        if ($user->hasRole('project_manager')) {
            return true;
        }
    }

    /**
     * Summary of viewAny
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->ownedTeams()->whereHas('projects.tasks')->exists() ||
            Task::whereHas('project.members', fn($q) => $q->where('users.id', $user->id))->exists();
    }
    /**
     * Summary of view
     * @param \App\Models\User $user
     * @param \App\Models\Task $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        return $task->project->members->contains($user);
    }
    /**
     * Summary of create
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return bool
     */
    public function create(User $user, Project $project): bool
    {
        return $project->members->contains($user);
    }
    /**
     * Summary of update
     * @param \App\Models\User $user
     * @param \App\Models\Task $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        return $task->project->members->contains($user);
    }
    /**
     * Summary of delete
     * @param \App\Models\User $user
     * @param \App\Models\Task $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->project->members->contains($user);
    }
}
