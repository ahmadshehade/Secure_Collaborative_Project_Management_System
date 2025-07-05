<?php

namespace App\Services\Auth;

use App\Interfaces\Repositories\Auth\AuthenticateRepositoryInterface;
use App\Interfaces\Services\Auth\AuthenticateServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;


class AuthenticateService implements AuthenticateServiceInterface
{

    protected $auth;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Auth\AuthenticateRepositoryInterface $auth
     */
    public function __construct(AuthenticateRepositoryInterface $auth)
    {
        $this->auth = $auth;
    }


    /**
     * Summary of register
     * @param mixed $request
     * @return array{token: mixed, user: mixed}
     */
    public function register($request)
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'success' => false,
                'message' => "Too many registration attempts. Please try again in {$seconds} seconds.",
            ];
        }


        RateLimiter::hit($key, 60);

        try {
            $user = $this->auth->register($request->validated());
            $user->assignRole('member');
            $token = $user->createToken('auth_user')->plainTextToken;

            RateLimiter::clear($key);

            return [
                'success' => true,
                'message' => 'Registration successful.',
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            Log::error("Registering Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Registration failed. Please try again later.',
            ];
        }
    }


    /**
     * Summary of login
     * @param mixed $request
     * @throws \Exception
     * @return array{token: mixed, user: mixed}
     */
    public function login($request)
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.',
                'retry_after_seconds' => $seconds,

            ];
        }

        $user = $this->auth->findByEmail($request['email']);

        if (!$user || !Hash::check($request['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
                'errors' => [
                    'email' => ['Invalid email or password.'],
                ],
            ];
        }

        RateLimiter::clear($key);

        $token = $user->createToken('auth_user')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ];
    }


    /**
     * Summary of throttleKey
     * @param mixed $request
     * @return string
     */
    protected function throttleKey($request): string
    {
        return Str::lower($request['email']) . '|' . request()->ip();
    }


    /**
     * Summary of logout
     * @param mixed $request
     * @throws \Exception
     * @return bool
     */
    public function logout($request)
    {
        $user = $request->user();
        if (!$user) {
            throw new \Exception('User Not Found', 404);
        }
        $user->tokens()->delete();
        return true;
    }
}
