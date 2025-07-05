<?php

namespace App\Http\Controllers\Api\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\MakeTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Interfaces\Services\Teams\TeamInterface;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $team;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Teams\TeamInterface $team
     */
    public function __construct(TeamInterface $team)
    {
        $this->team = $team;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->team->index();
        return response()->json([
            "data" => $data,
        ], 200);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Team\MakeTeamRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(MakeTeamRequest $request)
    {
        $data = $this->team->store($request);
        return response()->json([
            'data' => $data,
        ], 201);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->team->show($id);
        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\Team\UpdateTeamRequest $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeamRequest $request, $id)
    {
        $data = $this->team->update($id, $request);
        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->team->destroy($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Summary of indexMostActiveTeams
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexMostActiveTeams()
    {
        $teams = $this->team->getMostActiveTeams(10);

        return response()->json([
            'message' => 'Most active teams retrieved successfully',
            'success' => true,
            'data' => $teams
        ]);
    }
}
