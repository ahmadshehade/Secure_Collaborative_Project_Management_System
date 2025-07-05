<?php

namespace App\Services\Teams;

use App\Events\Team\CreationTeamEvent;
use App\Events\Team\DeletionTeamEvent;
use App\Events\Team\UpdationTeamEvent;
use App\Events\TeamMembersUpdated;
use App\Interfaces\Repositories\Teams\TeamRepositoryInterface;
use App\Interfaces\Services\Teams\TeamInterface;
use App\Models\Team;
use App\Traits\HasAttachments;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamService implements TeamInterface
{
    use AuthorizesRequests, HasAttachments;
    protected $team;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Teams\TeamRepositoryInterface $team
     */
    public function __construct(TeamRepositoryInterface $team)
    {
        $this->team = $team;
    }


    /**
     * Summary of index
     * @return array{data: mixed, message: string, success: bool}
     */
    public function index()
    {
        $this->authorize("viewAny", Team::class);
        $user = auth('api')->user();
        if ($user->hasRole('project_manager')) {

            $data = $this->team->getAllTeams();
        } else {
            $data = Team::visibleToUser($user)->with(['owner', 'members', 'projects']);
        }
        return [
            'success' => true,
            'message' => 'This Is All Teams',
            'data' => $data,
        ];
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function show($id)
    {
        $team = $this->team->getTeamById($id);
        $this->authorize('view', $team);

        return [
            'success' => true,
            'message' => 'This Is Team',
            'data' => $team,
        ];
    }


    /**
     * Summary of store
     * @param mixed $request
     * @return array{data: mixed, message: string, success: bool}
     */
    public function store($request)
    {
        try {
            DB::beginTransaction();
            $authUser = auth('api')->user();
            $validate = $request->validated();
            $members = $validate['members'] ?? [];
            $this->authorize('create', Team::class);
            $team = $this->team->createTeam($validate);
            if (!$authUser->hasRole('admin')) {
                $members[] = $authUser->id;
            }
            $this->team->attachMembers($team, array_unique($members));
            $team->load('members', 'owner');
            Cache::forget('most_active_teams_limit_10');
            $createdBy = auth('api')->user();
            event(new CreationTeamEvent($team, $createdBy));
            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Create Team',
                'data' => $team,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(" Fail To Make Team" . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Summary of update
     * @param mixed $teamId
     * @param mixed $request
     * @return array{data: mixed, message: string, success: bool}
     */
    public function   update($teamId, $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validated();
            $team = $this->team->getTeamById($teamId);
            $this->authorize("update", $team);
            $team = $this->team->updateTeam($teamId, $validate);
            Cache::forget('most_active_teams_limit_10');
            event(new TeamMembersUpdated($team));
            $updatedBy = auth('api')->user();
            event(new UpdationTeamEvent($team, $updatedBy));
            DB::commit();

            return [
                'success' => true,
                'message' => 'Successfully Update Team',
                'data' => $team,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(" Fail To update Team" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of destroy
     * @param mixed $teamId
     * @return array{message: string, success: bool}
     */
    public function destroy($teamId)
    {
        try {
            DB::beginTransaction();
            $team = $this->team->getTeamById($teamId);
            $team->load([
                'members',
                'owner',
                'projects.attachments',
                'projects.comments.attachments',
                'projects.tasks.attachments',
                'projects.tasks.comments.attachments'
            ]);

            $this->authorize('delete', $team);
            $teamData = [
                'id' => $team->id,
                'name' => $team->name,
                'owner_name' => optional($team->owner)->name,
            ];
            Cache::forget('most_active_teams_limit_10');

            event(new DeletionTeamEvent($teamData, auth('api')->user()));
            $this->team->deleteTeam($teamId);
            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Delete Team',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fail Delete Team' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of getMostActiveTeams
     * @param int $limit
     */
    public function getMostActiveTeams(int $limit = 10)
    {
        $this->authorize('viewAny', Team::class);
        $cacheKey = "most_active_teams_limit_{$limit}";
        return Cache::remember($cacheKey, 600, function () use ($limit) {
            return $this->team->getMostActiveTeams($limit);
        });
    }
}
