<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Services\Auth\AuthenticateServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  protected $auth;

  public function __construct(AuthenticateServiceInterface $auth)
  {
    $this->auth = $auth;
  }

  /**
   * Summary of login
   * @param \App\Http\Requests\Auth\LoginRequest $request
   * @return mixed|\Illuminate\Http\JsonResponse
   */
  public function login(LoginRequest $request)
  {
    $user = $this->auth->login($request);
    return response()->json([
      'data' => $user
    ], 200);
  }

  /**
   * Summary of register
   * @param \App\Http\Requests\Auth\RegisterRequest $request
   * @return mixed|\Illuminate\Http\JsonResponse
   */
  public function  register(RegisterRequest $request)
  {
    $user = $this->auth->register($request);
    return response()->json([

      'data' => $user,
    ], 200);
  }


  /**
   * Summary of logout
   * @param \Illuminate\Http\Request $request
   * @return mixed|\Illuminate\Http\JsonResponse
   */
  public function logout(Request $request)
  {
    $user = $this->auth->logout($request);
    return response()->json([
      'message' => 'Successfully logout User',
      'data' => $user
    ], 200);
  }
}
