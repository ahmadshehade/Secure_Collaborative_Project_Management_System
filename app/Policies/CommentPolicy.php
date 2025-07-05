<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{

    /**
     * Summary of viewAny
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin')
            || $user->hasRole('project_manager')
            || Project::whereHas('members', fn($q) => $q->where('users.id', $user->id))->exists()
            || Task::whereHas('project.members', fn($q) => $q->where('users.id', $user->id))->exists();
    }



    /**
     * Summary of view
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function view(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->hasRole('admin')
            || $user->hasRole('project_manager')
            || (
                $comment->commentable_type === Project::class &&
                $comment->commentable->members()->where('users.id', $user->id)->exists()
            )
            || (
                $comment->commentable_type === Task::class &&
                optional($comment->commentable->project)->members()->where('users.id', $user->id)->exists()
            );
    }


    /**
     * Summary of create
     * @param \App\Models\User $user
     * @param string $commentableType
     * @param int $commentableId
     * @return bool
     */
    public function create(User $user, string $commentableType, int $commentableId): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('project_manager')) {
            return true;
        }

        if ($commentableType === Project::class) {
            return Project::where('id', $commentableId)
                ->whereHas('members', fn($q) => $q->where('users.id', $user->id))
                ->exists();
        }

        if ($commentableType === Task::class) {
            return Task::where('id', $commentableId)
                ->whereHas('project.members', fn($q) => $q->where('users.id', $user->id))
                ->exists();
        }

        return false;
    }

    /**
     * Summary of update
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id == $comment->user_id;
    }

    /**
     * Summary of delete
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('project_manager')) {
            return true;
        }
        return $user->id == $comment->user_id;
    }
}
