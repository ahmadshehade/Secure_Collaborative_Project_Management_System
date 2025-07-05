<?php 

namespace App\Interfaces\Repositories\Users;

interface UserRepositoryInterface{

    public  function changeRole(int $id,array $data);

    public function deleteUser(int $id);

    public function getUser(int $id);

    public function getAllUsers(int $limit);
}