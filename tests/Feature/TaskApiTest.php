<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{

    use RefreshDatabase;

    protected $user;
    protected $project;
    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');

        $team = Team::factory()->create();

        $this->project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $this->user->id,
        ]);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'api']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);

        $this->project->members()->attach($this->user->id);
    }

    /**
     * Summary of test_can_list_tasks
     * @return void
     */
    public function test_can_list_tasks()
    {
        Task::factory()->count(3)->create(['project_id' => $this->project->id]);

        $response = $this->getJson('api/get/all/tasks');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['success', 'data']])
            ->assertJsonFragment(['success' => true]);
    }


    /**
     * Summary of test_can_create_task
     * @return void
     */
    public function test_can_create_task()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create(['role'=>'member']);


        $project->members()->attach($user->id);

        $creator = User::factory()->create(['role'=>'project_manager']);
        $creator->assignRole('project_manager');
        $this->actingAs($creator, 'api');

        $payload = [
            'name' => 'Test Task',
            'project_id' => $project->id,
            'assigned_to_user_id' => $user->id,
            'status' => 'pending',
            'description' => 'Task desc',
            'priority' => 'low',
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->postJson('api/make/task', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['success' => true])
            ->assertJsonPath('data.data.name', 'Test Task');

        $this->assertDatabaseHas('tasks', ['name' => 'Test Task']);
    }


    /**
     * Summary of test_can_show_a_task
     * @return void
     */
    public function test_can_show_a_task()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->getJson("api/get/task/{$task->id}");

        $response->assertOk()
            ->assertJsonFragment(['success' => true])
            ->assertJsonPath('data.data.id', $task->id);
    }

    /**
     * Summary of test_can_update_a_task
     * @return void
     */
    public function test_can_update_a_task()
    {

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'pending',

        ]);

        $payload = ['status' => 'completed'];

        $response = $this->postJson("api/update/task/{$task->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    /**
     * Summary of test_can_delete_a_task
     * @return void
     */
    public function test_can_delete_a_task()
    {
        $user = User::factory()->create(['role'=>'member'])->assignRole('member');
        $task = Task::factory()->create(['project_id' => $this->project->id,  'assigned_to_user_id' => $user->id]);

        $response = $this->deleteJson("api/delete/task/{$task->id}");

        $response->assertOk()
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Summary of test_can_get_completed_task_count_for_project
     * @return void
     */
    public function test_can_get_completed_task_count_for_project()
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'completed'
        ]);

        $response = $this->getJson("api/get/completed/tasks/count/{$this->project->id}");


        $response->assertOk()
            ->assertJsonFragment(['message' => 'Successfully Get Completed Tasks Count'])
            ->assertJsonPath('data.count', 1);
    }

    /**
     * Summary of test_user_without_permission_cannot_view_task
     * @return void
     */
    public function test_user_without_permission_cannot_view_task()
    {
        $unauthorizedUser = User::factory()->create(['role'=>'member']);
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($unauthorizedUser, 'api')
            ->getJson("api/get/task/{$task->id}");

        $response->assertStatus(403);
    }
    /**
     * Summary of test_user_without_permission_cannot_update_task
     * @return void
     */
    public function test_user_without_permission_cannot_update_task()
    {
        $unauthorizedUser = User::factory()->create(['role'=>'member']);
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($unauthorizedUser, 'api')
            ->postJson("api/update/task/{$task->id}", ['status' => 'completed']);

        $response->assertStatus(403);
    }

    /**
     * Summary of test_view_non_existing_task_returns_404
     * @return void
     */
    public function test_view_non_existing_task_returns_404()
    {
        $response = $this->getJson("api/get/task/9999999");

        $response->assertStatus(404);
    }

    /**
     * Summary of test_user_not_in_project_cannot_delete_task
     * @return void
     */
    public function test_user_not_in_project_cannot_delete_task()
    {
        $otherUser = User::factory()->create(['role'=>'member'])->assignRole('member');
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($otherUser, 'api')
            ->deleteJson("api/delete/task/{$task->id}");

        $response->assertStatus(403);
    }

    /**
     * Summary of test_create_task_with_invalid_data_returns_422
     * @return void
     */
    public function test_create_task_with_invalid_data_returns_422()
    {
        $this->user->assignRole('member');
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id);
        $managerUser = User::factory()->create()->assignRole('project_manager');
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $managerUser->id,
        ]);
        $this->actingAs($managerUser, 'api');
        $response = $this->postJson('api/make/task', [
            'status' => 'pending',
        ]);
        $response->assertStatus(422);
    }



}
