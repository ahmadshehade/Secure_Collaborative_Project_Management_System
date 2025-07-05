<?php



namespace Tests\Unit;

use App\Events\Project\ProjectCreationEvent;
use App\Interfaces\Repositories\Projects\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\User;
use App\Services\Projects\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $projectRepositoryMock;
    protected $projectService;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

       
        $user = User::factory()->create();
        $this->actingAs($user, 'api');


        $this->projectRepositoryMock = $this->mock(ProjectRepositoryInterface::class);


        $this->projectService = \Mockery::mock(ProjectService::class . '[authorize]', [$this->projectRepositoryMock])
            ->shouldAllowMockingProtectedMethods();

        $this->projectService
            ->shouldReceive('authorize')
            ->andReturn(true);
    }

    /**
     * Summary of test_store_creates_project_and_dispatches_event
     * @return void
     */
    public function test_store_creates_project_and_dispatches_event()
    {
        Event::fake();

       
        $team = \App\Models\Team::factory()->create();

        $validatedData = [
            'team_id' => $team->id,
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'open',
            'due_date' => '2025-12-31',
        ];

        $project = new Project($validatedData + ['id' => 1]);

        $this->projectRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($validatedData)
            ->andReturn($project);

        $fakeRequest = new class($validatedData) {
            private $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function validated()
            {
                return $this->data;
            }
        };

        $result = $this->projectService->store($fakeRequest);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Project::class, $result['data']);
        $this->assertEquals('Test Project', $result['data']->name);

        Event::assertDispatched(ProjectCreationEvent::class);
    }
}
