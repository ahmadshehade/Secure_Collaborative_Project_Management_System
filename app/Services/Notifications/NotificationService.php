<?php

namespace App\Services\Notifications;

use App\Interfaces\Repositories\Notifications\NotificationRepositoryInterface;
use App\Interfaces\Services\Notifications\NotificationInterface;

class NotificationService implements NotificationInterface
{

    protected $repo;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Notifications\NotificationRepositoryInterface $repo
     */
    public function __construct(NotificationRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
    /**
     * Summary of index
     * @param mixed $user
     * @param mixed $limit
     * @return array{data: mixed, message: string, success: bool}
     */
    public function index($user, $limit)
    {
        $data = $this->repo->getAll($user, $limit);
        return [
            'message' => 'Successfully Get All Notification to User',
            'data' => $data,
            'success' => true
        ];
    }
    /**
     * Summary of unread
     * @param mixed $user
     * @param mixed $limit
     * @return array{data: mixed, message: string, success: bool}
     */
    public function unread($user, $limit)
    {
        $data = $this->repo->getUnread($user, $limit);
        return [
            'message' => 'Successfully Get All Un Read Notification to User',
            'data' => $data,
            'success' => true
        ];
    }
    /**
     * Summary of markAsRead
     * @param mixed $user
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function markAsRead($user,  $id)
    {
        $data = $this->repo->markAsRead($user, $id);
        return [
            'message' => 'Successfully Make Read Notifications',
            'data' => $data,
            'success' => true
        ];
    }
    /**
     * Summary of destroy
     * @param mixed $user
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function destroy($user,  $id)
    {
        $data = $this->repo->delete($user, $id);

        return [
            'message' => 'Successfully Make Read Notifications',
            'data' => $data,
            'success' => true
        ];
    }
    /**
     * Summary of deleteAllRead
     * @param mixed $user
     * @return array{message: string, success: bool}
     */
    public function deleteAllRead($user)
    {
        $this->repo->deleteAllRead($user);
        return [
            'success' => true,
            'message' => 'All read notifications deleted successfully',
        ];
    }
    /**
     * Summary of allMarkSaRead
     * @param mixed $user
     * @return array{data: mixed, message: string, success: bool}
     */
    public function allMarkSaRead($user)
    {
        $data = $this->repo->allMarkSaRead($user);
        return [
            'message' => 'Successfully Read All notifications',
            'data' => $data,
            'success' => true
        ];
    }
}
