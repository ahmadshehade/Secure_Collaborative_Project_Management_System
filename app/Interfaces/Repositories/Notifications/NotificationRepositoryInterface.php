<?php 
namespace App\Interfaces\Repositories\Notifications;

 interface NotificationRepositoryInterface{

     public function getAll($user,$limit);
    public function getUnread($user,$limit);
    public function markAsRead($user, string $id);
    public function delete($user, string $id);

    public function deleteAllRead($user);

    public function allMarkSaRead($user);
 }