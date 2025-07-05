<?php

namespace App\Repositories\Auth;

use App\Interfaces\Repositories\Auth\AuthenticateRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticateRepository implements AuthenticateRepositoryInterface
{

    /**
     * Summary of register
     * @param array $data
     * @return User
     */
    public function register(array $data)
    {
        $user = new User();
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = 'member';
        $user->save();

        return $user;
    }

    /**
     * Summary of findByEmail
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }
}
