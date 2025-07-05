<?php

namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MakeTaskEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $createdBy;
    /**
     * Create a new event instance.
     */
    public function __construct($task,$createdBy)
    {
        $this->task = $task;
        $this->createdBy = $createdBy;
    }


}
