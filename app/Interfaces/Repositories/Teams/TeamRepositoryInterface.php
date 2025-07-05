<?php

namespace App\Interfaces\Repositories\Teams;

use App\Models\Team;

interface TeamRepositoryInterface
{
    public function getAllTeams(); 

    public function getTeamById(int $teamId); 

    public function createTeam(array $data);

    public function updateTeam(int $teamId, array $data);

    public function deleteTeam(int $teamId);

    public function getMostActiveTeams(int $limit = 10);

        public function attachMembers(Team $team, array $memberIds);
}
