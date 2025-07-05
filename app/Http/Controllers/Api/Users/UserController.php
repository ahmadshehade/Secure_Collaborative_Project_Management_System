<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Interfaces\Services\Users\UserInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Summary of user
     * @var 
     */
    protected $user;
    
    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Users\UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Summary of index
     * @param mixed $limit
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index($limit)
    {
        $user = $this->user->getAllUser($limit);
        return response()->json([$user], 200);
    }

    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\User\UpdateRoleRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $user = $this->user->updateRole($id, $request);
        return response()->json([$user], 200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = $this->user->show($id);
        return response()->json([$user], 200);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = $this->user->destroyUser($id);
        return response()->json([$user], 200);
    }
}
