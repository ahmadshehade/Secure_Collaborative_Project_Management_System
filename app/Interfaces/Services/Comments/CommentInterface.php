<?php 

namespace App\Interfaces\Services\Comments;

interface CommentInterface{

    public function getCommentsFor($type,$commentableId);

    public function addComment($request);

    public function updateComment($request,$id);

    public function deleteComment($id);

    public function  showComment($id);

}