<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{
    /**
     * Summary of before
     * @param \App\Models\User $user
     * 
     */
    public function before(User $user)
    {
        if (
            $user->hasRole('admin')
            || $user->hasRole('project_manager')
        ) {
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
        return   Project::whereHas('members', fn($q) => $q->where('users.id', $user->id))
            ->whereHas('attachments')
            ->exists() ||
            Task::whereHas('project.members', fn($q) => $q->where('users.id', $user->id))
            ->whereHas('attachments')
            ->exists() ||
            Comment::where('user_id', $user->id)
            ->whereHas('attachments')
            ->exists();
    }


    /**
     * Summary of view
     * @param \App\Models\User $user
     * @param \App\Models\Attachment $attachment
     * @return bool
     */
    public function view(User $user, Attachment $attachment): bool
    {
        $attachable = $attachment->attachable;
        if ($attachable instanceof Project) {
            return $attachable->members->contains($user);
        }
        if ($attachable instanceof Task) {
            return $attachable->project->members->contains($user);
        }
        if ($attachable instanceof Comment) {
            return $attachable->user_id === $user->id;
        }
        return false;
    }
}
