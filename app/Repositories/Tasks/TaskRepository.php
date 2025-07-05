<?php

namespace App\Repositories\Tasks;

use App\Interfaces\Repositories\Tasks\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository  implements TaskRepositoryInterface
{

    /**
     * Summary of create
     * @param array $data
     * @return Task
     */
    public function create(array $data)
    {
        $task = new Task();
        $task->fill($data);
        $task->save();
        return $task->load('project', 'user');
    }
    /**
     * Summary of updateTask
     * @param array $data
     * @param int $id
     * @return Task
     */
    public function updateTask(array $data, int $id)
    {
        $task = Task::findOrFail($id);
        $task->fill($data);
        $statusChanged = $task->isDirty('status');
        $task->save();
        $task->statusChanged = $statusChanged;
        return $task->load('project', 'user');
    }
    /**
     * Summary of delete
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return true;
    }
    /**
     * Summary of getTaskById
     * @param int $id
     * @return Task
     */
    public function  getTaskById(int $id)
    {
        return Task::with(['project', 'user', 'attachments'])->findOrFail($id);
    }
    /**
     * Summary of getTasks
     * @return \Illuminate\Database\Eloquent\Collection<int, Task>
     */
    public function getTasks()
    {
        return Task::with(['project', 'user'])->latest()->get();
    }
    /**
     * Summary of getCompletedTasksCount
     * @param int $projectId
     * @return int
     */
    public function getCompletedTasksCount(int $projectId)
    {
        return Task::where('project_id', $projectId)
            ->where('status', 'completed')
            ->count();
    }
}
