<?php

namespace App\Events\Comment;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

      public $comment;
      public $createdBy;
    /**
     * Summary of __construct
     * @param mixed $comment
     * @param mixed $createdBy
     */
    public function __construct($comment,$createdBy)
    {
        $this->comment = $comment;
        $this->createdBy = $createdBy;
    }


}
