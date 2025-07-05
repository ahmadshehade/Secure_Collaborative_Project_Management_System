<?php
namespace App\Listeners\Team;

use App\Events\Team\DeletionTeamEvent;
use App\Models\User;
use App\Notifications\Team\TeamDeleteNotifications;

class DeletionTeamListener
{
    /**
     * Summary of handle
     * @param \App\Events\Team\DeletionTeamEvent $event
     * @return void
     */
    public function handle(DeletionTeamEvent $event): void
    {
        $teamData = $event->teamData;
        $deletedBy = $event->deletedBy;

        $members = User::whereHas('teams', function($q) use ($teamData) {
            $q->where('team_id', $teamData['id']);
        })->get();

        foreach ($members as $member) {
            $member->notify(new TeamDeleteNotifications($teamData, $deletedBy));
        }

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new TeamDeleteNotifications($teamData, $deletedBy));
        }
    }
}

