<?php 

namespace App\Interfaces\Services\Projects;

interface ProjectInterface{

    public function  index();

    public function  store($request);

    public function show($id);

    public function update($id,$request);

    public function destroy($id);

    public function getProjectsWithLateTasks();
}