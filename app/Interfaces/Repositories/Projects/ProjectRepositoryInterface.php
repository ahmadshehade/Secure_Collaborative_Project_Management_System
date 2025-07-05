<?php 

namespace App\Interfaces\Repositories\Projects;

interface ProjectRepositoryInterface{

    public function getAllProjects();

    public function create(array $data);

    public function update(array $data,int $projectId);

    public function delete($id);

    public function getProjectById($id);

    public function getProjectsWithLateTasks();
}