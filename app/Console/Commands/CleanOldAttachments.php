<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldAttachments extends Command
{
    /**
     * Summary of signature
     * @var string
     */
    protected $signature = 'app:clean-old-attachments';

    /**
     * Summary of description
     * @var string
     */
    protected $description = 'Delete attachments related to completed projects older than 3 months';

    /**
     * Summary of handle
     * @return int
     */
    public function handle(): int
    {
        $thresholdDate = now()->subMonths(3);


        $projects = Project::where('status', 'completed')
            ->whereDate('updated_at', '<=', $thresholdDate)
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No completed projects older than 3 months found.');
            return Command::SUCCESS;
        }

        $deletedCount = 0;

        foreach ($projects as $project) {

            $deletedCount += $this->deleteAttachments($project);


            foreach ($project->tasks as $task) {
                $deletedCount += $this->deleteAttachments($task);


                foreach ($task->comments as $comment) {
                    $deletedCount += $this->deleteAttachments($comment);
                }
            }


            foreach ($project->comments as $comment) {
                $deletedCount += $this->deleteAttachments($comment);
            }
        }

        $this->info(" Total attachments deleted: {$deletedCount}");
        return Command::SUCCESS;
    }


    /**
     * Summary of deleteAttachments
     * @param mixed $model
     * @return int
     */
    private function deleteAttachments($model): int
    {
        $count = 0;

        if (!method_exists($model, 'attachments')) {
            return 0;
        }

        foreach ($model->attachments as $attachment) {
            if (Storage::disk($attachment->disk)->exists($attachment->path)) {
                Storage::disk($attachment->disk)->delete($attachment->path);
                $this->line("Deleted file: {$attachment->path}");
            }

            $attachment->delete();
            $this->line("Deleted DB record ID: {$attachment->id}");
            $count++;
        }

        return $count;
    }
}
