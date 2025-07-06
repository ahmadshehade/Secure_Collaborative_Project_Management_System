<?php

namespace Tests\Feature;

use App\Events\Task\DeleteTaskEvent;
use App\Events\Task\MakeTaskEvent;
use App\Events\Task\TaskCompletedEvent;
use App\Events\Task\TaskUpdateEvent;
use App\Listeners\Task\SendTaskCompletedNotification;
use App\Models\Task;
use App\Models\User;
use App\Notifications\Task\TaskCompletedNotification;
use App\Notifications\Task\TaskCreateNotifications;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_make_task_event_is_dispatched()
    {
        Event::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create();


        event(new MakeTaskEvent($task, $user));


        Event::assertDispatched(MakeTaskEvent::class, function ($event) use ($task, $user) {
            return $event->task->id === $task->id && $event->createdBy->id === $user->id;
        });
    }


    /**
     * Summary of test_task_update_event_is_dispatched
     */
    public function test_task_update_event_is_dispatched()
    {
        Event::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create();

        event(new TaskUpdateEvent($task, $user));

        Event::assertDispatched(TaskUpdateEvent::class, function ($event) use ($task, $user) {
            return $event->task->id === $task->id && $event->updatedBy->id === $user->id;
        });
    }


    /**
     * Summary of test_task_update_event_is_dispatched_when_task_is_updated
     */
    public function test_task_update_event_is_dispatched_when_task_is_updated()
    {
        Event::fake();
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create()->assignRole('project_manager');
        $this->actingAs($user, 'api');

        $project = \App\Models\Project::factory()->create([
            'created_by_user_id' => $user->id,
        ]);
        $project->members()->attach($user->id);

        $task = Task::factory()->create([
            'status' => 'pending',
            'project_id' => $project->id,
        ]);

        $response = $this->postJson("/api/update/task/{$task->id}", [
            'status' => 'completed'
        ]);

        $response->assertOk();

        Event::assertDispatched(TaskUpdateEvent::class, function ($event) use ($task, $user) {
            return $event->task->id === $task->id && $event->updatedBy->id === $user->id;
        });
    }


    /**
     * Summary of test_delete_task_event_is_dispatched
     */
    public function test_delete_task_event_is_dispatched()
    {
        Event::fake();
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create(['role'=>'project_manager'])->assignRole('project_manager');
        $this->actingAs($user, 'api');

        $project = \App\Models\Project::factory()->create([
            'created_by_user_id' => $user->id,
        ]);
        $project->members()->attach($user->id);

        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->deleteJson("/api/delete/task/{$task->id}");

        $response->assertOk();

        Event::assertDispatched(DeleteTaskEvent::class, function ($event) use ($task, $user) {
            return $event->task->id === $task->id && $event->deletedBy->id === $user->id;
        });
    }

    /**
     * Summary of test_send_task_creation_notifications_listener_dispatches_notification
     * @return void
     */
    public function test_send_task_creation_notifications_listener_dispatches_notification()
    {
        Notification::fake();
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $assigned = User::factory()->create(['role'=>'member']);
        $manager = User::factory()->create()->assignRole('project_manager');

        $task = Task::factory()->create([
            'assigned_to_user_id' => $assigned->id,
        ]);

        event(new MakeTaskEvent($task, $user));

        Notification::assertSentTo([$assigned], TaskCreateNotifications::class);
        Notification::assertSentTo([$manager], TaskCreateNotifications::class);
        Notification::assertNotSentTo($user, TaskCreateNotifications::class);
    }


    /**
     * Summary of test_send_task_creation_mail_listener_dispatches_job
     */
    public function test_send_task_creation_mail_listener_dispatches_job()
    {
        Bus::fake();
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $assigned = User::factory()->create(['role'=>'member'])->assignRole('member');
        $task = Task::factory()->create(['assigned_to_user_id' => $assigned->id]);

        $event = new MakeTaskEvent($task, $user);
        $listener = new \App\Listeners\Task\SendTaskCreationMail();

        $listener->handle($event);

        Bus::assertDispatched(\App\Jobs\Task\MakeTaskJob::class, function ($job) use ($task, $user) {
            return $job->task->id === $task->id &&
                $job->createdBy->id === $user->id &&
                $job->recipients->contains(function ($recipient) use ($task) {
                    return $recipient->id === $task->assigned_to_user_id;
                });
        });
    }

    /**
     * Summary of test_task_update_notifications_listener_sends_notifications
     * @return void
     */
    public function test_task_update_notifications_listener_sends_notifications()
    {
        Notification::fake();
        $this->seed(AdminTableSeeder::class);
        $updatedBy = User::factory()->create();
        $assignedUser = User::factory()->create();
        $manager = User::factory()->create(['role'=>'project_manager'])->assignRole('project_manager');

        $task = Task::factory()->create([
            'assigned_to_user_id' => $assignedUser->id,
        ]);
        $task->user()->associate($assignedUser)->save();

        $event = new TaskUpdateEvent($task, $updatedBy);
        $listener = new \App\Listeners\Task\TaskUpdateNotifications();

        $listener->handle($event);

        Notification::assertSentTo([$assignedUser], \App\Notifications\Task\TaskUpdateNotifications::class);
        Notification::assertSentTo([$manager], \App\Notifications\Task\TaskUpdateNotifications::class);
        Notification::assertNotSentTo($updatedBy, \App\Notifications\Task\TaskUpdateNotifications::class);
    }

    /**
     * Summary of test_task_updation_mail_listener_dispatches_job
     */
    public function test_task_updation_mail_listener_dispatches_job()
    {
        Bus::fake();
        $this->seed(AdminTableSeeder::class);
        $updatedBy = User::factory()->create();
        $assignedUser = User::factory()->create(['role'=>'member'])->assignRole('member');

        $task = Task::factory()->create([
            'assigned_to_user_id' => $assignedUser->id,
        ]);
        $task->user()->associate($assignedUser)->save();

        $event = new TaskUpdateEvent($task, $updatedBy);
        $listener = new \App\Listeners\Task\TaskUpdationMail();

        $listener->handle($event);

        Bus::assertDispatched(\App\Jobs\Task\TaskUpdateJob::class, function ($job) use ($task, $updatedBy, $assignedUser) {
            return $job->task->id === $task->id &&
                $job->updatedBy->id === $updatedBy->id &&
                $job->recipients->contains(fn($u) => $u->id === $assignedUser->id);
        });
    }

    /**
     * Summary of test_send_task_completed_notification_listener_sends_notifications
     * @return void
     */
    public function test_send_task_completed_notification_listener_sends_notifications()
    {
        Notification::fake();
        $this->seed(AdminTableSeeder::class);
        $creator = User::factory()->create(['role'=>'member'])->assignRole('member');
        $member1 = User::factory()->create(['role'=>'member'])->assignRole('member');
        $member2 = User::factory()->create(['role'=>'member'])->assignRole('member');

        $task = Task::factory()->create();
        $project = $task->project;


        $project->userCreated()->associate($creator)->save();

        $project->members()->attach([$member1->id, $member2->id]);


        $task->load('project.members', 'project.userCreated');


        $listener = new SendTaskCompletedNotification();
        $listener->handle(new TaskCompletedEvent($task));


        Notification::assertSentTo([$member1, $member2, $creator], TaskCompletedNotification::class);
    }
}
