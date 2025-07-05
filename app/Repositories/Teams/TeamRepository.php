<?php

namespace App\Repositories\Teams;

use App\Interfaces\Repositories\Teams\TeamRepositoryInterface;
use App\Models\Team;

class TeamRepository  implements TeamRepositoryInterface
{

    /**
     * Summary of getAllTeams
     * @return \Illuminate\Database\Eloquent\Collection<int, Team>
     */
    public function getAllTeams()
    {
        return Team::with(['owner', 'members', 'projects'])
            ->orderBy("created_at", "desc")
            ->get();
    }

    /**
     * Summary of getTeamById
     * @param int $teamId
     * @return Team
     */
    public function getTeamById(int $teamId)
    {
        return Team::with(['owner', 'members', 'projects'])->findOrFail($teamId);
    }

    /**
     * Summary of createTeam
     * @param array $data
     * @return Team
     */
    public function createTeam(array $data)
    {
        $members = $data['members'] ?? [];
        unset($data['members']);
        $team = new Team();
        $team->owner_id = auth('api')->user()->id;
        $team->name = $data['name'];
        $team->save();

        return  $team->load('owner', 'members', 'projects');
    }

    /**
     * Summary of attachMembers
     * @param \App\Models\Team $team
     * @param array $memberIds
     * @return void
     */
    public function attachMembers(Team $team, array $memberIds): void
    {
        $team->members()->attach($memberIds);
    }

    /**
     * Summary of updateTeam
     * @param int $teamId
     * @param array $data
     * @return Team
     */
    public function updateTeam(int $teamId, array $data)
    {
        $team = Team::findOrFail($teamId);
        $members = $data['members'] ?? null;
        unset($data['members']);
        $team->update($data);

        $memberIds = collect($members)->push($team->owner_id)->unique()->toArray();
        $team->members()->sync($memberIds);



        return $team->load('owner', 'members', 'projects');
    }

    /**
     * Summary of deleteTeam
     * @param int $teamId
     * @return bool
     */
    public function deleteTeam(int $teamId)
    {
        $team = Team::findOrFail($teamId);
        $team->delete();
        return true;
    }

    /**
     * Summary of getMostActiveTeams
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, Team>
     */
    public function getMostActiveTeams(int $limit = 10)
    {
        return Team::with(['owner'])
            ->withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->take($limit)
            ->get();
    }
}
