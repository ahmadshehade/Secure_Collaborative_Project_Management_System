<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Summary of test_admin_can_create_team
     * @return void
     */
    public function  test_admin_can_create_team()
    {
        $this->seed(AdminTableSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/make/team', [
            'name' => 'Test Team',
            'members' => [],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.success', true)
            ->assertJsonPath('data.message', 'Successfully Create Team');
    }

    /**
     * Summary of test_member_can_view_own_teams
     * @return void
     */
    public function test_member_can_view_own_teams()
    {
        $this->seed(AdminTableSeeder::class);

        $user = User::factory()->create();
        $user->assignRole("member");

        $team = Team::factory()->create(['owner_id' => $user->id]);
        $team->members()->attach($user->id);

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/get/team/{$team->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.success', true);
    }

    /**
     * Summary of test_team_owner_can_update_team
     * @return void
     */
   public function test_team_owner_can_update_team()
{
    $this->seed(AdminTableSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('member');

    $team = Team::factory()->create(['owner_id' => $user->id]);
    $team->members()->attach($user->id);

    $token = $user->createToken('test_auth')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson("/api/update/team/{$team->id}", [
        'name' => 'Updated Team Name',
        'members' => [$user->id],
    ]);



    $response->assertStatus(200);  

   
   
}



    /**
     * Summary of test_team_owner_can_delete_team
     * @return void
     */
    public function test_team_owner_can_delete_team()
    {
        $this->seed(AdminTableSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $team = Team::factory()->create(['owner_id' => $user->id]);
        $team->members()->attach($user->id);
        $id = $team->id;

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/delete/team/{$id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.success', true);
    }


    /**
     * Summary of test_user_cannot_view_unauthorized_team
     * @return void
     */
    public function test_user_cannot_view_unauthorized_team()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('member');

        $team = Team::factory()->create();

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/get/team/{$team->id}");

        $response->assertForbidden(); // 403
    }


    /**
     * Summary of test_user_cannot_update_unauthorized_team
     * @return void
     */
    public function test_user_cannot_update_unauthorized_team()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('member');

        $team = Team::factory()->create(); // ليس مالكه

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/update/team/{$team->id}", [
            'name' => 'New Name',
            'members' => [],
        ]);

        $response->assertForbidden();
    }

    /**
     * Summary of test_user_cannot_delete_unauthorized_team
     * @return void
     */
    public function test_user_cannot_delete_unauthorized_team()
    {
        $this->seed(AdminTableSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('member');

        $team = Team::factory()->create();

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/delete/team/{$team->id}");

        $response->assertForbidden();
    }

    /**
     * Summary of test_user_without_permission_cannot_create_team
     * @return void
     */
    public function test_user_without_permission_cannot_create_team()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test_auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/make/team', [
            'name' => 'Unauthorized Team',
            'members' => [],
        ]);

        $response->assertForbidden();
    }
}
