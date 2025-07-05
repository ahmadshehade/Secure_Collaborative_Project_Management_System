<?php

namespace Tests\Unit;

use App\Events\Task\TaskCompletedEvent;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Observers\TaskObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskObserverTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([TaskCompletedEvent::class]);

        Task::observe(TaskObserver::class);
    }

    /**
     * Summary of test_task_observer_dispatches_event_when_completed
     */
    public function test_task_observer_dispatches_event_when_completed()
    {
        Event::fake([TaskCompletedEvent::class]);

        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $team = Team::factory()->create(['owner_id' => $user->id]);

        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);


        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'in_progress',
        ]);


        $task->refresh()->update(['status' => 'completed']);


        Event::assertDispatched(TaskCompletedEvent::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    }
}
