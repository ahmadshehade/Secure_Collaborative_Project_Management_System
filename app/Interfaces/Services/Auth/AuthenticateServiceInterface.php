<?php 

namespace App\Interfaces\Services\Auth;

interface AuthenticateServiceInterface{

    public function   register($request);

    public  function login($request);

    public  function logout($request);
}