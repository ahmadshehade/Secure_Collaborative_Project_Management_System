<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Summary of model
     * @var 
     */
    protected $model = Attachment::class;

    /**
     * Summary of definition
     * @return array{attachable_id:  attachable_type: string, disk: string, file_name: string, file_size: int, mime_type: string, path: string}
     */
    public function definition(): array
    {
        return [
            'attachable_id' => Project::factory(),
            'attachable_type' => Project::class,
            'file_name' => $this->faker->word() . '.jpg',
            'path' => 'attachments/Project/' . $this->faker->numberBetween(1, 10) . '/' . Str::random(10) . '.jpg',
            'disk' => 'public',
            'file_size' => $this->faker->numberBetween(10000, 500000),
            'mime_type' => 'image/jpeg',
        ];
    }

    /**
     * Summary of forProject
     * @return AttachmentFactory
     */
    public function forProject()
    {
        return $this->state(function () {
            $project = Project::factory()->create();
            return [
                'attachable_id' => $project->id,
                'attachable_type' => Project::class,
                'path' => 'attachments/Project/' . $project->id . '/' . Str::random(10) . '.jpg',
            ];
        });
    }

    /**
     * Summary of forTask
     * @return AttachmentFactory
     */
    public function forTask()
    {
        return $this->state(function () {
            $task = Task::factory()->create();
            return [
                'attachable_id' => $task->id,
                'attachable_type' => Task::class,
                'path' => 'attachments/Task/' . $task->id . '/' . Str::random(10) . '.jpg',
            ];
        });
    }

    /**
     * Summary of forComment
     * @return AttachmentFactory
     */
    public function forComment()
    {
        return $this->state(function () {
            $comment = Comment::factory()->create();
            return [
                'attachable_id' => $comment->id,
                'attachable_type' => Comment::class,
                'path' => 'attachments/Comment/' . $comment->id . '/' . Str::random(10) . '.jpg',
            ];
        });
    }
}
