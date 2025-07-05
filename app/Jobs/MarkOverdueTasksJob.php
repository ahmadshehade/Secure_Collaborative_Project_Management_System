<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MarkOverdueTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Summary of handle
     * @return void
     */
    public function handle(): void
    {
        $overdueTasks = Task::whereNot('status', ['completed'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        foreach ($overdueTasks as $task) {
            $task->update(['status' => 'pending']);
            Log::info("Task marked as overdue: ID {$task->id}");
        }
    }
}
