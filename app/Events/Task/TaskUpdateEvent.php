<?php

namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

     public $task;
    public $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct($task,$updatedBy)
    {
        $this->task = $task;
        $this->updatedBy = $updatedBy;
    }

}
