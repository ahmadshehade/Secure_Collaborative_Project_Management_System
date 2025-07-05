<?php

namespace App\Repositories\Users;

use App\Interfaces\Repositories\Users\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Summary of changeRole
     * @param int $id
     * @param array $data
     * @return void
     */
    public  function changeRole(int $id, array $data)
    {

        $user = User::findOrFail($id);
        $user->role = $data['role'];
        $user->save();

        $user->syncRoles([$user->role]);
    }
    /**
     * Summary of deleteUser
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return true;
    }
    /**
     * Summary of getUser
     * @param int $id
     * @return User
     */
    public function getUser(int $id)
    {
        return User::findOrFail($id);
    }
    /**
     * Summary of getAllUsers
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function getAllUsers(int $limit)
    {
        $users = User::orderBy('id', 'desc')->take($limit)->get();
        return $users;
    }
}
