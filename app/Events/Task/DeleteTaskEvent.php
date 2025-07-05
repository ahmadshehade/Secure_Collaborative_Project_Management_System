<?php

namespace App\Events\Task;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteTaskEvent
{
    use Dispatchable, SerializesModels;

    public $task;
    public $deletedBy;

    /**
     * Summary of __construct
     * @param mixed $task
     * @param mixed $deletedBy
     */
    public function __construct($task, $deletedBy)
    {
        $this->task = $task;
        $this->deletedBy = $deletedBy;
    }

    
}
