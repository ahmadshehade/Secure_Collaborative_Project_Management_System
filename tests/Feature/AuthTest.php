<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use function Pest\Laravel\withHeaders;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected string $loginRoute = '/api/login';



    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('test@example.com|127.0.0.1');
    }

    /**
     * Summary of test_user_can_login_with_correct_credentials
     * @return void
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson($this->loginRoute, [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'success',
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ],
        ]);
    }

    /**
     * Summary of test_login_fails_with_invalid_credentials
     * @return void
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson($this->loginRoute, [
            'email' => 'test@example.com',
            'password' => 'Password123!#',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'success',
                'message',
                'errors' => [
                    'email'
                ]
            ]
        ]);
    }


    /**
     * Summary of test_login_rate_limiter_blocks_after_5_attempts
     * @return void
     */
    public function test_login_rate_limiter_blocks_after_5_attempts()
    {
        $email = 'test@example.com';
        $password = 'Password123!#';


        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('DifferentPassword123!'),
        ]);


        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson($this->loginRoute, [
                'email' => $email,
                'password' => $password,
            ]);
        }

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Too many login attempts. Please try again later.',
        ]);
    }


    /**
     * Summary of test_user_can_logout_and_token_deleted
     * @return void
     */
    public function test_user_can_logout_and_token_deleted()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            "message" => 'Successfully logout User',
        ]);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
