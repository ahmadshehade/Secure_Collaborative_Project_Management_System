<?php

namespace App\Jobs\Task;

use App\Mail\Task\TaskMakeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MakeTaskJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;

    public $createdBy;
    public $recipients;
    /**
     * Create a new job instance.
     */
    public function __construct($task, $createdBy, $recipients)
    {
        $this->task = $task;
        $this->createdBy = $createdBy;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            foreach ($this->recipients as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new TaskMakeMail($this->task, $this->createdBy));
                }
            }
        } catch (\Exception $e) {
            Log::error("Fail Send Email To Make Task" . $e->getMessage());
        }
    }
}
