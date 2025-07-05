<?php 

namespace App\Interfaces\Repositories\Tasks;


interface TaskRepositoryInterface{

    public function create(array $data);

    public function updateTask(array $data,int $id);

    public function delete(int $id);

    public function  getTaskById(int $id);

    public function getTasks();

    public  function getCompletedTasksCount(int $projectId);
}