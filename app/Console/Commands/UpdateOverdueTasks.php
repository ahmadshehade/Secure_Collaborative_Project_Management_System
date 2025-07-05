<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateOverdueTasks extends Command
{
    protected $signature = 'app:update-overdue-tasks';

    protected $description = 'Update overdue tasks to pending status if they are not completed.';

    /**
     * Summary of handle
     * @return int
     */
    public function handle(): int
    {
        $overdueTasks = Task::where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['completed'])
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('There are no overdue tasks at the moment.');
            return Command::SUCCESS;
        }

        foreach ($overdueTasks as $task) {
            $task->update(['status' => 'pending']);
            Log::info("Overdue task updated (ID: {$task->id}) to pending status.");
            $this->line(" Task #{$task->id} has been updated to pending.");
        }

        $this->info('All overdue tasks have been updated.');
        return Command::SUCCESS;
    }
}
