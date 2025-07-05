<?php 
namespace App\Interfaces\Services\Tasks;

interface TaskInterface{

    public function index();

    public  function  store($request);

    public function show($id);

    public function update($id,$request);

    public function destroy($id);

    public function getCompletedTasksCount($projectId);
}