<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Project;

class AssignedUserIsProjectMember implements Rule
{
    protected $projectId;

    /**
     * Summary of __construct
     * @param mixed $projectId
     */
    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }


    /**
     * Summary of passes
     * @param mixed $attribute
     * @param mixed $value
     */
    public function passes($attribute, $value)
    {
        if (!$this->projectId) {
            return false; 
        }

        $project = Project::with('members')->find($this->projectId);
        if (!$project) {
            return false;
        }

        return $project->members->contains('id', $value);
    }

    public function message()
    {
        return 'The assigned user must be a member of the specified project.';
    }
}
