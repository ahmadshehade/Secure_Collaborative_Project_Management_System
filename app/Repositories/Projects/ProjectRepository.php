<?php

namespace App\Repositories\Projects;

use App\Interfaces\Repositories\Projects\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\Team;
use Carbon\Carbon;

class ProjectRepository implements ProjectRepositoryInterface
{



  /**
   * Summary of getAllProjects
   * @return \Illuminate\Database\Eloquent\Builder<Project>
   */
  public function getAllProjects()
  {
    return Project::with([
      'team',
      'members',
      'tasks',
      'comments',
      'attachments',
      'userCreated'
    ])->orderBy('created_at', 'desc');
  }


  /**
   * Summary of create
   * @param array $data
   * @return Project
   */
  public function create(array $data)
  {
    $project = new Project();

    $project->team_id = $data["team_id"];
    $project->name = $data["name"];
    $project->description = $data["description"] ?? null;
    $project->status = $data["status"] ?? 'pending';
    $project->due_date = $data["due_date"] ?? null;
    $project->created_by_user_id = auth('api')->user()->id;

    $project->save();
    $team = Team::findOrFail($data['team_id']);
    $memberIds = $team->members()->pluck('users.id')->toArray();
    $project->members()->attach($memberIds);

    return $project->load(['team', 'members', 'tasks', 'comments', 'attachments', 'userCreated']);
  }

  /**
   * Summary of update
   * @param array $data
   * @param int $projectId
   * @return Project
   */
  public function update(array $data, int $projectId)
  {
    $project = Project::with(['userCreated'])->findOrFail($projectId);

    $project->update($data);

    return $project->load(['team', 'members', 'tasks', 'comments', 'attachments', 'userCreated']);
  }

  /**
   * Summary of delete
   * @param mixed $id
   * @return bool
   */
  public function delete($id)
  {
    $project = Project::findOrFail($id);
    $project->delete();
    return true;
  }


  /**
   * Summary of getProjectById
   * @param mixed $id
   * @return Project|\Illuminate\Database\Eloquent\Collection<int, Project>
   */
  public function getProjectById($id)
  {
    $project = Project::with([
      'team',
      'members',
      'tasks',
      'comments',
      'attachments',
      'userCreated'
    ])->withCount('members')->findOrFail($id);
    return $project;
  }


  /**
   * Summary of getProjectsWithLateTasks
   * @return \Illuminate\Database\Eloquent\Collection<int, Project>
   */
  public function  getProjectsWithLateTasks()
  {

    $data = Project::whereHas('tasks', function ($q) {
      $q->where('due_date', '<', Carbon::today());
    })->with(['tasks', function ($q) {
      $q->where('due_date', '<', Carbon::today());
    }])->get();

    return $data;
  }
}
