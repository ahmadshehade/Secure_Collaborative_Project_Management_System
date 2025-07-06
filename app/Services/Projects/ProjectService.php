<?php

namespace App\Services\Projects;

use App\Events\Project\ProjectCreationEvent;
use App\Events\Project\ProjectDeletionEvent;
use App\Events\Project\ProjectUpdationEvent;
use App\Events\ProjectTeamChanged;
use App\Interfaces\Repositories\Projects\ProjectRepositoryInterface;
use App\Interfaces\Services\Projects\ProjectInterface;
use App\Models\Project;
use App\Models\Team;
use App\Traits\HasAttachments;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectService implements ProjectInterface
{
    use AuthorizesRequests, HasAttachments;

    protected $project;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Projects\ProjectRepositoryInterface $project
     */
    public function __construct(ProjectRepositoryInterface $project)
    {
        $this->project = $project;
    }


    /**
     * Summary of index
     * @return array{data: mixed, message: string, success: bool}
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        $user = auth('api')->user();

        $query = $this->project->getAllProjects();

        if (!$user->hasRole('admin') && !$user->hasRole('project_manager')) {
            $query = Project::visibleToUser($user)->with(['team', 'members', 'tasks']);
        }

        $projects = $query->get();

        return [
            'message' => 'Successfully Get Projects',
            'success' => true,
            'data' => $projects,
        ];
    }


    /**
     * Summary of store
     * @param mixed $request
     * @return array{data: mixed, success: bool}
     */
    public function store($request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $team = Team::findOrFail($validate['team_id']);
            $this->authorize('create', [Project::class, $team]);
            $project = $this->project->create($validate);
            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                $this->uploadAttachments($validate['attachment'], $project->id, Project::class, 'private');
            }
            $createdBy = auth('api')->user();
            event(new ProjectCreationEvent($project, $createdBy));
            DB::commit();
            return ['success' => true, 'data' => $project];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed  Make  Project" . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Summary of show
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function show($id)

    {
        $data = $this->project->getProjectById($id);
        $this->authorize('view', $data);
        return [
            'message' => 'Successfully get Project ',
            "success" => true,
            "data" => $data
        ];
    }


    /**
     * Summary of update
     * @param mixed $id
     * @param mixed $request
     * @return array{data: mixed, message: string, success: bool}
     */
    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $project = $this->project->getProjectById($id);
            $old = $project->team->id;
            $this->authorize("update", $project);
            $project = $this->project->update($validate, $id);
            $new = $project->team->id;
            if ($old != $new) {
                event(new ProjectTeamChanged($project));
            }
            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                if ($project->attachments->isNotEmpty()) {
                    $this->deleteAttachments($project);
                }
                $this->uploadAttachments($validate['attachment'], $project->id, Project::class, 'private');
            }
            $updatedBy = auth('api')->user();
            event(new ProjectUpdationEvent($project, $updatedBy));
            DB::commit();
            return [
                'message' => 'Successfully Update Project ',
                "success" => true,
                "data" => $project
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed Update Project" . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Summary of destroy
     * @param mixed $id
     * @return array{message: string, success: bool}
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $project = $this->project->getProjectById($id);
            $this->authorize("delete", $project);
            $deletedBy = auth('api')->user();
            event(new ProjectDeletionEvent($project, $deletedBy));
            $this->project->delete($id);
            DB::commit();
            return [
                'message' => 'Successfully delete Project',
                'success' => true
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Fail Delete Project: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Summary of getProjectsWithLateTasks
     * @return array{data: mixed, message: string, success: bool}
     */
    public function getProjectsWithLateTasks()
    {
        $user = auth('api')->user();
        $this->authorize('viewAny', Project::class);
        if ($user->hasRole('admin') && $user->hasRole('project_manager')) {
            $projects = $this->project->getProjectsWithLateTasks();
        } else {
            $projects = Project::visibleToUser($user)
                ->whereHas('tasks', function ($q) {
                    $q->where('due_date', '<', Carbon::today());
                })
                ->with(['tasks' => function ($q) {
                    $q->where('due_date', '<', Carbon::today());
                }])
                ->get();
        }
        return [
            'message' => 'Successfully Get All Project with Late Task',
            'success' => true,
            'data' => $projects
        ];
    }
}
