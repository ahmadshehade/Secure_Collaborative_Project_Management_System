<?php

namespace Tests\Feature;

use App\Jobs\MarkOverdueTasksJob;
use App\Jobs\ProcessImageAttachment;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Task\MakeTaskJob;
use App\Jobs\Task\TaskUpdateJob;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueueApiTest extends TestCase
{

    /**
     * Summary of test_make_task_job_is_dispatched
     */
    public function test_make_task_job_is_dispatched()
    {
        Queue::fake();

        $task = Task::factory()->create();
        $createdBy = User::factory()->create();
        $recipients = User::factory()->count(3)->create();


        MakeTaskJob::dispatch($task, $createdBy, $recipients);


        Queue::assertPushed(MakeTaskJob::class, function ($job) use ($task, $createdBy, $recipients) {
            return $job->task->id === $task->id
                && $job->createdBy->id === $createdBy->id
                && $job->recipients->count() === $recipients->count();
        });
    }


    /**
     * Summary of test_task_update_job_is_dispatched
     */
    public function test_task_update_job_is_dispatched()
    {
        Queue::fake();

        $task = Task::factory()->create();
        $updatedBy = User::factory()->create();
        $recipients = User::factory()->count(2)->create();

        TaskUpdateJob::dispatch($task, $updatedBy, $recipients);

        Queue::assertPushed(TaskUpdateJob::class, function ($job) use ($task, $updatedBy, $recipients) {
            return $job->task->id === $task->id
                && $job->updatedBy->id === $updatedBy->id
                && $job->recipients->count() === $recipients->count();
        });
    }


    /**
     * Summary of test_mark_overdue_tasks_job
     * @return void
     */
    public function test_mark_overdue_tasks_job()
    {
       
        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('info');
        Log::shouldReceive('warning'); 

        
        $task = Task::factory()->create([
            'status' => 'in_progress',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        
        Task::factory()->create([
            'status' => 'completed',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        (new MarkOverdueTasksJob())->handle();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }
}
