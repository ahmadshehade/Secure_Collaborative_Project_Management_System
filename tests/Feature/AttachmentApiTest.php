<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Traits\HasAttachments;
use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class AttachmentApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'api']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
    }

    /**
     * Summary of test_admin_can_view_all_attachments
     * @return void
     */
    public function test_admin_can_view_all_attachments()
    {
        $this->seed(AdminTableSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Attachment::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/get/all/attachments');

        $response->assertOk();

        $response->assertJsonStructure([

            'data'
        ]);
    }

    /**
     * Summary of test_user_can_view_project_attachment_if_member
     * @return void
     */
    public function test_user_can_view_project_attachment_if_member()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->members()->attach($user);

        $attachment = Attachment::factory()->create([
            'attachable_type' => Project::class,
            'attachable_id' => $project->id,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/get/all/attachments');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $attachment->id]);
    }

    /**
     * Summary of test_user_can_view_task_attachment_if_member_in_project
     * @return void
     */
    public function test_user_can_view_task_attachment_if_member_in_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->members()->attach($user);

        $task = Task::factory()->create(['project_id' => $project->id]);

        $attachment = Attachment::factory()->create([
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/get/all/attachments');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $attachment->id]);
    }

    /**
     * Summary of user_can_view_own_comment_attachments_only
     * @return void
     */
    public function user_can_view_own_comment_attachments_only()
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $attachment = Attachment::factory()->create([
            'attachable_type' => Comment::class,
            'attachable_id' => $comment->id,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/get/all/attachments');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $attachment->id]);
    }

    /**
     * Summary of test_user_cannot_view_others_comment_attachments
     * @return void
     */
    public function test_user_cannot_view_others_comment_attachments()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $comment = Comment::factory()->create(['user_id' => $other->id]);

        $attachment = Attachment::factory()->create([
            'attachable_type' => Comment::class,
            'attachable_id' => $comment->id,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/get/all/attachments');

        $response->assertForbidden();
        $response->assertJsonMissing(['id' => $attachment->id]);
    }




    /**
     * Summary of test_upload_attachments_stores_files_and_records
     * @return void
     */
    public function test_upload_attachments_stores_files_and_records()
    {
        Storage::fake('private');
        Queue::fake();

        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);
        $project = Project::factory()->create();

        $uploader = new class {
            use HasAttachments;
        };

        $uploader->uploadAttachments([$file], $project->id, get_class($project), 'private');

        $attachment = Attachment::firstWhere('attachable_id', $project->id);

        $this->assertNotNull($attachment, 'Attachment record not created');

        Storage::disk('private')->assertExists($attachment->path);

        Queue::assertPushed(\App\Jobs\ProcessImageAttachment::class);
    }


    /**
     * Summary of test_delete_attachments_removes_files_and_records
     * @return void
     */
    public function test_delete_attachments_removes_files_and_records()
    {
        Storage::fake('private');


        $project = Project::factory()->create();

        $attachment = Attachment::factory()->create([
            'attachable_id' => $project->id,
            'attachable_type' => get_class($project),
            'disk' => 'private',
            'path' => "attachments/Project/{$project->id}/file.jpg",
            'file_name' => 'file.jpg',
        ]);


        Storage::disk('private')->put($attachment->path, 'dummy content');

        $uploader = new class {
            use HasAttachments;
        };


        Storage::disk('private')->assertExists($attachment->path);


        $uploader->deleteAttachments($project);


        Storage::disk('private')->assertMissing($attachment->path);


        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }
}
