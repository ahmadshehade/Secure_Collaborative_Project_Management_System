<?php

namespace App\Repositories\Comments;

use App\Interfaces\Repositories\Comments\CommentRepositoryInterface;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * Summary of create
     * @param array $data
     * @return Comment
     */
    public function create(array $data)
    {
        $comment = new Comment();
        $comment->fill($data);
        $comment->user_id = auth('api')->user()->id;
        $comment->save();
        $comment->load('latestAttachment');
        return $comment->load('latestAttachment', 'user');
    }
    /**
     * Summary of update
     * @param int $id
     * @param array $data
     * @return Comment
     */
    public function update(int $id, array $data)
    {

        $comment = Comment::findOrFail($id);
        $comment->update($data);
        $comment->load('latestAttachment', 'user');
        return $comment;
    }
    /**
     * Summary of delete
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return true;
    }

    /**
     * Summary of getCommentById
     * @param int $id
     * @return Comment
     */
    public function getCommentById(int $id)
    {
        $comment = Comment::with(['latestAttachment', 'user'])->findOrFail($id);
        return $comment;
    }

    /**
     * Summary of getCommentsFor
     * @param string $type
     * @param int $commentableId
     * @throws \InvalidArgumentException
     * @return \Illuminate\Database\Eloquent\Collection<int, Comment>
     */
    public function getCommentsFor(string $type, int $commentableId)
    {
        $commentableType = match ($type) {
            'project' => Project::class,
            'task' => Task::class,
            default => throw new \InvalidArgumentException("Unsupported type [$type]")
        };

        return Comment::with(['user', 'latestAttachment'])
            ->where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->latest()
            ->get();
    }
}
