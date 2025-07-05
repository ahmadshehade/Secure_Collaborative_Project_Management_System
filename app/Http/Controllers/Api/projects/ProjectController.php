<?php

namespace App\Http\Controllers\Api\projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Interfaces\Services\Projects\ProjectInterface;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $project;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Projects\ProjectInterface $project
     */
    public function __construct(ProjectInterface $project){
      $this->project = $project;
    }

 
    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index(){
        $data=$this->project->index();
        return response()->json([
            'data'=> $data
        ],200);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Project\StoreProjectRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreProjectRequest $request){
        $data=$this->project->store($request);
        return response()->json([
            'data'=> $data
        ],201);
    }
    
 
    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Project\UpdateProjectRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id,UpdateProjectRequest $request){
        $data=$this->project->update($id,$request);
        return response()->json([
            'data'=> $data
        ],200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id){
        $data=$this->project->show($id);
        return response()->json([
            'data'=> $data
        ],200);
    }
    
    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id){
       $data=$this->project->destroy($id);
       return response()->json([
        'data'=>$data,
       ],200); 
    }

    /**
     * Summary of getProjectsWithLateTasks
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getProjectsWithLateTasks(){
         $data=$this->project->getProjectsWithLateTasks();
         return response()->json([
            'data'=> $data
         ],200);
    }

}
