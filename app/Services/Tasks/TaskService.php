<?php

namespace App\Services\Tasks;

use App\Events\Task\DeleteTaskEvent;
use App\Events\Task\MakeTaskEvent;
use App\Events\Task\TaskUpdateEvent;
use App\Interfaces\Repositories\Tasks\TaskRepositoryInterface;
use App\Interfaces\Services\Tasks\TaskInterface;
use App\Models\Project;
use App\Models\Task;
use App\Traits\HasAttachments;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService implements TaskInterface
{

    use AuthorizesRequests, HasAttachments;
    protected $task;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Tasks\TaskRepositoryInterface $task
     */
    public function __construct(TaskRepositoryInterface $task)
    {
        $this->task = $task;
    }

    /**
     * Summary of index
     * @return array{data: mixed, success: bool}
     */
    public function index()
    {
        $user = auth('api')->user();
        $this->authorize("viewAny", Task::class);



        if ($user->hasRole('project_manager')) {

            $tasks = $this->task->getTasks();
        } else {
            $tasks = Task::visibleToUser($user)->get();
        }

        return [
            'success' => true,
            'data' => $tasks
        ];
    }

    /**
     * Summary of store
     * @param mixed $request
     * @return array{data: mixed, message: string, success: bool}
     */
    public  function  store($request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $project = Project::findOrFail($validate['project_id']);
            $this->authorize('create', [Task::class, $project]);
            $data = $this->task->create($validate);
            if ($data->wasRecentlyCreated) {
                $projectId = $project->id;
                Cache::forget("project_{$projectId}_completed_tasks_count");
            }
            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                $this->uploadAttachments($validate['attachment'], $data->id, Task::class, 'private');
            }
            $createdBy = auth('api')->user();
            event(new MakeTaskEvent($data, $createdBy));
            DB::commit();


            return [
                'message' => 'Successfully make new Task',
                'success' => true,
                'data' => $data
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Fail Make Task" . $e->getMessage());
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
        $data = $this->task->getTaskById($id);
        $this->authorize("view", $data);
        return [
            "message" => 'Successfully get Task',
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
            $task = $this->task->getTaskById($id);
            $this->authorize("update", $task);
            $validate = $request->validated();
            $data = $this->task->updateTask($validate, $id);
            if (!empty($data->statusChanged)) {
                Cache::forget("project_{$data->project_id}_completed_tasks_count");
            }

            if (isset($validate['attachment']) && is_array($validate['attachment'])) {
                if ($data->attachments->isNotEmpty()) {

                    $this->deleteAttachments($data);
                }
                $this->uploadAttachments($validate['attachment'], $data->id, Task::class, 'private');
            }
            $updatedBy=auth('api')->user();
             event(new TaskUpdateEvent($task,$updatedBy));
            DB::commit();

            return [
                "message" => "Successfully Update task",
                "success" => true,
                "data" => $data
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Fail TO Update Task" . $e->getMessage());
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
            $task = $this->task->getTaskById($id);
            $this->authorize("delete", $task);
            $deletedBy=auth("api")->user();
              event(new DeleteTaskEvent($task,$deletedBy));
            $projectId = $task->project_id;
            Cache::forget("project_{$projectId}_completed_tasks_count");
            $this->task->delete($id);
            DB::commit();
            return [
                "message" => "Successfully Delete Task",
                "success" => true,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Fail Delete Task" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of getCompletedTasksCount
     * @param mixed $projectId
     * @return array{count: mixed, message: string}
     */
    public function getCompletedTasksCount($projectId)
    {
        $cacheKey = "project_{$projectId}_completed_tasks_count";

        return ["count" => Cache::remember($cacheKey, now()->addMinutes(10), function () use ($projectId) {
            return $this->task->getCompletedTasksCount($projectId);
        }), 'message' => 'Successfully Get Completed Tasks Count'];
    }
}
