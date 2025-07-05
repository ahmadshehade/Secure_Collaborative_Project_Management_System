<?php

namespace App\Jobs\Task;

use App\Mail\Task\TaskUpdateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskUpdateJob implements ShouldQueue
{
    use Queueable;
    public $task;
    public  $updatedBy;
    public $recipients;
    /**
     * Create a new job instance.
     */
    public function __construct($task, $updatedBy, $recipients)
    {
        $this->task = $task;
        $this->updatedBy = $updatedBy;
        $this->recipients = $recipients;
    }



    /**
     * Summary of handle
     * @return void
     */
    public function handle(): void
    {
        try {
            foreach ($this->recipients as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new TaskUpdateMail($this->task, $this->updatedBy));
                }
            }
        } catch (\Exception $e) {
            Log::error(message: "Fail Send Email With Update Task " . $e->getMessage());
        }
    }
}
