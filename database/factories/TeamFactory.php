<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    /**
     * Summary of model
     * @var 
     */
    protected $model = Team::class;

    /**
     * Summary of definition
     * @return array{name: string, owner_id: UserFactory}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'owner_id' => User::factory(),
        ];
    }
}
