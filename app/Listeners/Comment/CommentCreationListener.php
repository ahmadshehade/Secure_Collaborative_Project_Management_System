<?php


namespace App\Listeners\Comment;

use App\Events\Comment\CommentCreationEvent;
use App\Notifications\Comment\CreationCommentNotification;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class CommentCreationListener
{

    /**
     * Summary of handle
     * @param \App\Events\Comment\CommentCreationEvent $event
     * @return void
     */
    public function handle(CommentCreationEvent $event): void
    {
        $comment = $event->comment;
        $createdBy = $event->createdBy;

        $notifiables = collect();

        if ($comment->commentable_type === Project::class) {
            $project = $comment->commentable;
            if ($project) {
                $notifiables = $notifiables
                    ->merge($project->members)
                    ->push($project->userCreated);
            }
        }

        if ($comment->commentable_type === Task::class) {
            $task = $comment->commentable;
            if ($task && $task->project) {
                $project = $task->project;
                $notifiables = $notifiables
                    ->merge($project->members)
                    ->push($project->userCreated);
            }
        }

        $adminsAndManagers = User::role(['admin', 'project_manager'])->get();
        $notifiables = $notifiables->merge($adminsAndManagers);

        $notifiables = $notifiables->unique('id')->filter(function ($user) use ($createdBy) {
            return $user && $createdBy && $user->id !== $createdBy->id;
        });

        foreach ($notifiables as $user) {
            $user->notify(new CreationCommentNotification($comment, $createdBy));
        }
    }
}
