<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Summary of test_admin_can_create_project
     * @return void
     */
    public function test_admin_can_create_project()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test_exa')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/make/project', [
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
            'status' => 'pending',
            'name' => "test project",
            "description" => "fafe  description",



        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'success',
                'data',
            ]
        ]);
    }

    /**
     * Summary of test_admin_can_view_all_projects
     * @return void
     */
    public function test_admin_can_view_all_projects()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test_exa')->plainTextToken;

        Project::factory()->create(['team_id' => $team->id, 'created_by_user_id' => $user->id]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/get/all/projects');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['message', 'success', 'data']]);
    }

    /**
     * Summary of test_team_owner_can_update_project
     * @return void
     */
    public function test_team_owner_can_update_project()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test_exa')->plainTextToken;
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/update/project/{$project->id}", [
            'name' => 'Updated Project Name'
        ]);

        $response->assertJsonStructure([
            'data' => [
                'success',
                'data',
            ]
        ]);
    }
    /**
     * Summary of test_project_creator_can_delete_project
     * @return void
     */
    public function test_project_creator_can_delete_project()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->assignRole('project_manager');
        $token = $user->createToken('test_exa')->plainTextToken;

        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/delete/project/{$project->id}");


        $response->assertOk()
            ->assertJsonFragment(['success' => true]);
    }

    /**
     * Summary of test_project_show_endpoint
     * @return void
     */
    public function test_project_show_endpoint()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test_exa')->plainTextToken;


        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/get/project/{$project->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $project->id]);
    }


    /**
     * Summary of test_get_projects_with_late_tasks_endpoint
     * @return void
     */
    public function test_get_projects_with_late_tasks_endpoint()
    {
        $this->seed(AdminTableSeeder::class);
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        $normalUser = User::factory()->create();
        $normalUser->assignRole('member');
        $team = Team::factory()->create([
            'owner_id' => $normalUser->id,
        ]);
        $adminToken = $adminUser->createToken('test_exa')->plainTextToken;
        $memberToken = $normalUser->createToken('test_exa')->plainTextToken;
        $projectWithLateTask = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $normalUser->id,
        ]);
        $projectWithLateTask->members()->attach($normalUser->id);
        Task::factory()->create([
            'project_id' => $projectWithLateTask->id,
            'due_date' => Carbon::yesterday(),
            'status' => 'pending',
        ]);
        $projectWithoutLateTask = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $normalUser->id,
        ]);
        Task::factory()->create([
            'project_id' => $projectWithoutLateTask->id,
            'due_date' => Carbon::tomorrow(),
            'status' => 'pending',
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/get/projects/with/late/task');
        $response->assertStatus(200);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $memberToken,
        ])->getJson('/api/get/projects/with/late/task');
        $response->assertStatus(200);
    }


    /**
     * Summary of test_user_without_permission_cannot_view_project
     * @return void
     */
    public function test_user_without_permission_cannot_view_project()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $anotherUser->id]);

        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id + 1,
        ]);


        $response = $this->actingAs($user, 'api')
            ->getJson("/api/get/project/{$project->id}");


        $response->assertStatus(403);
    }
    /**
     * Summary of test_project_manager_can_view_any_project
     * @return void
     */
    public function test_project_manager_can_view_any_project()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('project_manager');

        $team = Team::factory()->create();
        $project = Project::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/get/project/{$project->id}");

        $response->assertStatus(200);
    }

    /**
     * Summary of test_project_owner_can_view_their_project
     * @return void
     */
    public function test_project_owner_can_view_their_project()
    {
        $user = User::factory()->create();

        $team = Team::factory()->create(['owner_id' => $user->id]);
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/get/project/{$project->id}");

        $response->assertStatus(200);
    }


    /**
     * Summary of test_project_deleted_from_database
     * @return void
     */
    public function test_project_deleted_from_database()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('project_manager');

        $team = Team::factory()->create(['owner_id' => $user->id]);
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);

        $this->actingAs($user, 'api')->deleteJson("/api/delete/project/{$project->id}")
            ->assertOk();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /**
     * Summary of test_viewing_non_existing_project_returns_404
     * @return void
     */
    public function test_viewing_non_existing_project_returns_404()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user, 'api')
            ->getJson("/api/get/project/999999")
            ->assertStatus(404);
    }

    /**
     * Summary of test_update_project_with_invalid_data_returns_422
     * @return void
     */
    public function test_update_project_with_invalid_data_returns_422()
    {

        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $team = Team::factory()->create();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $user->id,
        ]);

        $this->actingAs($user, 'api')
            ->postJson("/api/update/project/{$project->id}", [
                'name' => '',
            ])
            ->assertStatus(422);
    }
    /**
     * Summary of test_user_without_permission_cannot_delete_project
     * @return void
     */
    public function test_user_without_permission_cannot_delete_project()
    {
        $this->seed(AdminTableSeeder::class);
        $owner = User::factory()->create()->assignRole('member');

        $otherUser = User::factory()->create()->assignRole('member');

        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'created_by_user_id' => $owner->id,
        ]);

        $this->actingAs($otherUser, 'api')
            ->deleteJson("/api/delete/project/{$project->id}")
            ->assertStatus(403);
    }
}
