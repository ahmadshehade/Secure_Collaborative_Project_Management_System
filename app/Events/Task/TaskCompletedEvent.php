<?php

namespace App\Events\Task;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCompletedEvent
{
    use Dispatchable, SerializesModels;

    public Task $task;


    /**
     * Summary of __construct
     * @param \App\Models\Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}

