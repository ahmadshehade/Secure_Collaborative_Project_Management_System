<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Summary of before
     * @param \App\Models\User $user
     *
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin')) {
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
        return $user->hasRole('project_manager') ||
            $user->ownedTeams()->whereHas('projects')->exists() ||
            Project::whereHas('members', fn($q) => $q->where('users.id', $user->id))->exists();
    }

    /**
     * Summary of view
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return bool
     */
    public function view(User $user, Project $project): bool
    {
        return $user->hasRole('project_manager') ||
            $project->team->owner_id === $user->id ||
            $project->members->contains($user) ||
            $project->created_by_user_id === $user->id;
    }

    /**
     * Summary of create
     * @param \App\Models\User $user
     * @param \App\Models\Team $team
     * @return bool
     */
    public function create(User $user, Team $team): bool
    {
        return $user->hasRole('project_manager') || $team->owner_id === $user->id;
    }

    /**
     * Summary of update
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return bool
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasRole('project_manager') ||
            $project->team->owner_id === $user->id ||
            $project->created_by_user_id === $user->id;
    }

    /**
     * Summary of delete
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return bool
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('project_manager') ||
            $project->team->owner_id === $user->id ||
            $project->created_by_user_id === $user->id;
    }
}
