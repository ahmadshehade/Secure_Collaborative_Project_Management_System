<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Summary of test_description_mutator_strips_tags_and_trims
     * @return void
     */
    public function test_description_mutator_strips_tags_and_trims()
    {
        $project = new Project();
        $project->description = '  <b>Hello</b> <script>alert(1)</script>  ';
        $this->assertEquals('Hello alert(1)', $project->description);
    }
    /**
     * Summary of test_created_at_accessor_formats_date
     * @return void
     */
    public function test_created_at_accessor_formats_date()
    {
        $project = new Project();
        $project->created_at = '2025-07-03 14:23:45';

        $this->assertEquals('2025-07-03 14:23', $project->created_at);
    }

    /**
     * Summary of test_updated_at_accessor_formats_date
     * @return void
     */
    public function test_updated_at_accessor_formats_date()
    {
        $project = new Project();
        $project->updated_at = '2025-07-03 18:30:00';

        $this->assertEquals('2025-07-03 18:30', $project->updated_at);
    }


       /**
        * Summary of test_it_sets_name_to_lowercase_and_returns_with_ucwords
        * @return void
        */
       public function test_it_sets_name_to_lowercase_and_returns_with_ucwords()
    {
        $rawName = 'laRAVel proJECt';

        
        $project = Project::factory()->create([
            'name' => $rawName,
        ]);

        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => strtolower($rawName), 
        ]);

       
        $this->assertEquals('Laravel Project', $project->name);
    }
}
