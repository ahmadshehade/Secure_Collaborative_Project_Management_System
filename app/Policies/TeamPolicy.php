<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
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
        return $user->teams()->exists() || $user->ownedTeams()->exists();
    }
    /**
     * Summary of view
     * @param \App\Models\User $user
     * @param \App\Models\Team $team
     * @return bool
     */
    public function view(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id || $team->members->contains($user);
    }

    /**
     * Summary of create
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole('member');
    }

    /**
     * Summary of update
     * @param \App\Models\User $user
     * @param \App\Models\Team $team
     * @return bool
     */
    public function update(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id;
    }
    /**
     * Summary of delete
     * @param \App\Models\User $user
     * @param \App\Models\Team $team
     * @return bool
     */
    public function delete(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id;
    }
}
