<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{

    use RefreshDatabase;

    protected $admin;
    protected $projectManager;
    protected $user;
    protected $project;
    protected $task;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminTableSeeder::class);
        $this->admin = User::factory()->create()->assignRole('admin');
        $this->projectManager = User::factory()->create()->assignRole('project_manager');
        $this->user = User::factory()->create();

        $this->project = Project::factory()->create(['created_by_user_id' => $this->projectManager->id]);
        $this->project->members()->attach($this->user);


        $this->task = Task::factory()->create(['project_id' => $this->project->id]);
        $this->task->project->members()->attach($this->user);
    }


    /**
     * Summary of test_admin_can_view_comments_for_project
     * @return void
     */
    public function test_admin_can_view_comments_for_project()
    {
        $this->actingAs($this->admin, 'api')
            ->getJson("/api/get/All/comment/project/{$this->project->id}")
            ->assertStatus(200);
    }

    /**
     * Summary of test_member_can_view_comments_for_task
     * @return void
     */
    public function test_member_can_view_comments_for_task()
    {
        $this->actingAs($this->user, 'api')
            ->getJson("/api/get/All/comment/task/{$this->task->id}")
            ->assertStatus(200);
    }
    /**
     * Summary of test_user_cannot_view_comments_for_unrelated_project
     * @return void
     */
    public function test_user_cannot_view_comments_for_unrelated_project()
    {
        $otherProject = Project::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/api/get/All/comment/project/{$otherProject->id}")
            ->assertStatus(403);
    }

    /**
     * Summary of test_project_manager_can_create_comment
     * @return void
     */
    public function test_project_manager_can_create_comment()
    {
        $payload = [
            'commentable_type' => 'project',
            'commentable_id' => $this->project->id,
            'content' => 'Test comment',
        ];

        $this->actingAs($this->projectManager, 'api')
            ->postJson('/api/make/comment', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['success' => true]);
    }
    /**
     * Summary of test_member_can_create_comment_on_project
     * @return void
     */
    public function test_member_can_create_comment_on_project()
    {
        $payload = [
            'commentable_type' => 'project',
            'commentable_id' => $this->project->id,
            'content' => 'Member comment',
        ];

        $this->actingAs($this->user, 'api')
            ->postJson('/api/make/comment', $payload)
            ->assertStatus(201);
    }

    /**
     * Summary of test_user_cannot_create_comment_on_unrelated_task
     * @return void
     */
    public function test_user_cannot_create_comment_on_unrelated_task()
    {
        $otherTask = Task::factory()->create();

        $payload = [
            'commentable_type' => 'task',
            'commentable_id' => $otherTask->id,
            'content' => 'Unauthorized comment',
        ];

        $this->actingAs($this->user, 'api')
            ->postJson('/api/make/comment', $payload)
            ->assertStatus(422);
    }

    /**
     * Summary of test_user_can_update_own_comment
     * @return void
     */
    public function test_user_can_update_own_comment()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'content' => 'Old content',
        ]);

        $payload = [
            'commentable_type' => 'project',
            'commentable_id' => $this->project->id,
            'content' => 'Updated content',
        ];

        $this->actingAs($this->user, 'api')
            ->postJson("/api/update/comment/{$comment->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated content']);
    }
    /**
     * Summary of test_user_cannot_update_others_comment
     * @return void
     */
    public function test_user_cannot_update_others_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'content' => 'Other content',
        ]);

        $payload = [
            'commentable_type' => 'project',
            'commentable_id' => $this->project->id,
            'content' => 'Attempt update',
        ];

        $this->actingAs($this->user, 'api')
            ->postJson("/api/update/comment/{$comment->id}", $payload)
            ->assertStatus(403);
    }

    /**
     * Summary of test_admin_can_delete_any_comment
     * @return void
     */
    public function test_admin_can_delete_any_comment()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $this->actingAs($this->admin, 'api')
            ->deleteJson("/api/delete/comment/{$comment->id}")
            ->assertStatus(200);
    }
    /**
     * Summary of test_user_can_delete_own_comment
     * @return void
     */
    public function test_user_can_delete_own_comment()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/api/delete/comment/{$comment->id}")
            ->assertStatus(200);
    }
    /**
     * Summary of test_user_cannot_delete_others_comment
     * @return void
     */
    public function test_user_cannot_delete_others_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/api/delete/comment/{$comment->id}")
            ->assertStatus(403);
    }
}
