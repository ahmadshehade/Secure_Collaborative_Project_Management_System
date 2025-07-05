<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Interfaces\Services\Tasks\TaskInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $task;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Tasks\TaskInterface $task
     */
    public function __construct(TaskInterface $task)
    {

        $this->task = $task;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index()
    {
        $data = $this->task->index();
        return response()->json(["data" => $data], 200);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Task\StoreTaskRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $this->task->store($request);
        return response()->json(["data" => $data], 201);
    }



    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Task\UpdateTaskRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateTaskRequest $request)
    {
        $data = $this->task->update($id, $request);
        return response()->json(["data" => $data], 200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->task->show($id);
        return response()->json(["data" => $data], 200);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->task->destroy($id);
        return response()->json(["data" => $data], 200);
    }

    /**
     * Summary of getCompletedTasksCount
     * @param mixed $projectId
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getCompletedTasksCount($projectId)
    {
        $data = $this->task->getCompletedTasksCount($projectId);
        return response()->json(["data" => $data], 200);
    }
}
