<?php

namespace App\Services\Users;

use App\Interfaces\Repositories\Users\UserRepositoryInterface;
use App\Interfaces\Services\Users\UserInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class UserService implements UserInterface
{
    use AuthorizesRequests;
    /**
     * Summary of user
     * @var 
     */
    protected $user;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Users\UserRepositoryInterface $user
     */
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Summary of updateRole
     * @param mixed $id
     * @param mixed $request
     * @return array{data: mixed, message: string, success: bool}
     */
    public function  updateRole($id, $request)
    {
        try {
            $validate = $request->validated();

            $user = $this->user->changeRole($id, $validate);
            return [
                'success' => true,
                'message' => 'Successfully change Role To User',
                'data' => $user
            ];
        } catch (\Exception $e) {
            Log::error("Fail To Change Role" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of destroyUser
     * @param mixed $id
     * @return array{message: string, success: bool}
     */
    public function destroyUser($id)
    {
        $user = $this->user->deleteUser($id);
        return [
            "success" => true,
            "message" => "Successfully Delete User"
        ];
    }


    /**
     * Summary of getAllUser
     * @param mixed $limit
     * @return array{data: mixed, message: string, success: bool}
     */
    public function getAllUser($limit)
    {
        $user = $this->user->getAllUsers($limit);
        return [
            "success" => true,
            "message" => "Successfully Get  Users",
            "data" => $user
        ];
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return array{data: mixed, message: string, success: bool}
     */
    public function show($id)
    {
        $user = $this->user->getUser($id);
        return [
            "success" => true,
            "message" => "Successfully Get User",
            "data" => $user
        ];
    }
}
