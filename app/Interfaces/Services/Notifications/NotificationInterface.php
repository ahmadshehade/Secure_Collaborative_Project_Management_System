<?php 

namespace App\Interfaces\Services\Notifications;

  interface NotificationInterface {
     public function index($user,$limit);
    public function unread($user, $limit);
    public function markAsRead($user,  $id);
    public function destroy($user,  $id);

    public function deleteAllRead($user);

    public function allMarkSaRead($user);
  }