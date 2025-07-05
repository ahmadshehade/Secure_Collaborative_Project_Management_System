<?php

namespace App\Services\Comments;

use App\Events\Comment\CommentCreationEvent;
use App\Interfaces\Repositories\Comments\CommentRepositoryInterface;
use App\Interfaces\Services\Comments\CommentInterface;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Traits\HasAttachments;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentService implements CommentInterface
{

    use AuthorizesRequests, HasAttachments;
    protected $comment;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Comments\CommentRepositoryInterface $comment
     */
    public function __construct(CommentRepositoryInterface $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Summary of getCommentsFor
     * @param mixed $type
     * @param mixed $commentableId
     * @throws \InvalidArgumentException
     */
    public function getCommentsFor($type, $commentableId)
    {
        $user = auth('api')->user();

        $commentableType = match ($type) {
            'project' => Project::class,
            'task' => Task::class,
            default => throw new \InvalidArgumentException("Unsupported type [$type]"),
        };


        if ($user->hasRole('admin') || $user->hasRole('project_manager')) {
            return $this->comment->getCommentsFor($type, $commentableId);
        }


        if ($commentableType === Project::class) {
            $project = Project::findOrFail($commentableId);

            if ($project->members->contains($user)) {
                return $this->comment->getCommentsFor($type, $commentableId);
            }
        }

        if ($commentableType === Task::class) {
            $task = Task::with('project.members')->findOrFail($commentableId);

            if ($task->project && $task->project->members->contains($user)) {
                return $this->comment->getCommentsFor($type, $commentableId);
            }
        }

        abort(403, 'You are not authorized to view these comments.');
    }


    /**
     * Summary of addComment
     * @param mixed $request
     * @throws \InvalidArgumentException
     * @return array{data: mixed, message: string, success: bool}
     */
    public function addComment($request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $validate['commentable_type'] = match ($validate['commentable_type']) {
                'project' => Project::class,
                'task' => Task::class,
                default => throw new \InvalidArgumentException('Invalid commentable type.')
            };
            $this->authorize('create', [Comment::class, $validate['commentable_type'], $validate['commentable_id']]);
            $comment = $this->comment->create($validate);
            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                $this->uploadAttachments($validate['attachment'], $comment->id, Comment::class, 'private');
            }
            event(new CommentCreationEvent($comment,auth('api')->user()));
            DB::commit();
            return [
                'message' => 'Successfully Create Comment',
                'success' => true,
                'data' => $comment
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fail Make Comment :" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of updateComment
     * @param mixed $request
     * @param mixed $id
     * @throws \InvalidArgumentException
     * @return array{data: mixed, message: string, success: bool}
     */
    public function updateComment($request, $id)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $validate['commentable_type'] = match ($validate['commentable_type']) {
                'project' => Project::class,
                'task' => Task::class,
                default => throw new \InvalidArgumentException('Invalid commentable type.')
            };
            $comment = $this->comment->getCommentById($id);

            $this->authorize('update', $comment);
            $comment = $this->comment->update($id, $validate);
            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                if ($comment->attachments->isNotEmpty()) {
                    $this->deleteAttachments($comment);
                }
                $this->uploadAttachments($validate['attachment'], $comment->id, Comment::class, 'private');
            }
            DB::commit();
            return [
                'message' => 'Successfully Update Comment',
                'success' => true,
                'data' => $comment
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fail Update Comment :" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of deleteComment
     * @param mixed $id
     * @return array{message: string, success: bool}
     */
    public function deleteComment($id)
    {
        try {
            DB::beginTransaction();
            $comment = $this->comment->getCommentById($id);
            $this->authorize('delete', $comment);
            if ($comment->attachments->isNotEmpty()) {
                $this->deleteAttachments($comment);
            }
            $this->comment->delete($id);
            DB::commit();
            return [
                "message" => "Successfully Deleted Comment",
                'success' => true
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fail Delete Comment" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of showComment
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function  showComment($id)
    {
        $comment = $this->comment->getCommentById($id);
        $this->authorize('view', $comment);
        return [
            'message' => 'SuccessFuly Get Comment',
            'success' => true,
            'data' => $comment
        ];
    }
}
