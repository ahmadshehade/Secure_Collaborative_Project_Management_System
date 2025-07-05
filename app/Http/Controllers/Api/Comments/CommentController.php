<?php

namespace App\Http\Controllers\Api\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Interfaces\Services\Comments\CommentInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $comments;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Comments\CommentInterface $comment
     */
    public function __construct(CommentInterface $comment)
    {
        $this->comments = $comment;
    }

    /**
     * Summary of index
     * @param mixed $type
     * @param mixed $commentId
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index($type, $commentId)
    {
        $comments = $this->comments->getCommentsFor($type, $commentId);
        return response()->json([$comments], 200);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Comment\StoreCommentRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request)
    {
        $comment = $this->comments->addComment($request);
        return response()->json([$comment], 201);
    }

    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Comment\UpdateCommentRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateCommentRequest $request)
    {
        $comment = $this->comments->updateComment($request, $id);
        return response()->json([$comment], 200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $comment = $this->comments->showComment($id);
        return response()->json([$comment], 200);
    }


    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $comment = $this->comments->deleteComment($id);
        return response()->json([$comment], 200);
    }
}
