<?php

namespace App\Repositories\Notifications;

use App\Interfaces\Repositories\Notifications\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * Summary of getAll
     * @param mixed $user
     * @param mixed $limit
     */
    public function getAll($user, $limit)
    {
        return $user->notifications()->latest()->take($limit)->get();
    }

    /**
     * Summary of getUnread
     * @param mixed $user
     * @param mixed $limit
     */
    public function getUnread($user, $limit)
    {
        return $user->unreadNotifications()->latest()->take($limit)->get();
    }

    public function markAsRead($user, string $id)
    {
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return $notification;
    }
    /**
     * Summary of delete
     * @param mixed $user
     * @param string $id
     */
    public function delete($user, string $id)
    {
        $notification = $user->notifications()
            ->whereNotNull('read_at')->where('id', $id)->firstOrFail();
        return $notification->delete();
    }
    /**
     * Summary of deleteAllRead
     * @param mixed $user
     */
    public function deleteAllRead($user)
    {
        return $user->notifications()->whereNotNull('read_at')->delete();
    }
    /**
     * Summary of allMarkSaRead
     * @param mixed $user
     */
    public function allMarkSaRead($user)
    {
        $notifications = $user->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return $notifications;
    }
}
