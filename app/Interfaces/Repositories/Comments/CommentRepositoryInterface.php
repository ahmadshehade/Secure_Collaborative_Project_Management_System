<?php 

namespace App\Interfaces\Repositories\Comments;

interface CommentRepositoryInterface{

    public function create(array $data);

    public function update( int $id, array $data);

    public function delete( int $id );

    public function getCommentById(int $id);

 public function getCommentsFor(string $type, int $commentableId);
}