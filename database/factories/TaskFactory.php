<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->sentence(3),
      'description' => $this->faker->paragraph,
      'status' => 'pending',
      'priority' => 'medium',
      'due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
      'project_id' => Project::factory(),
      'assigned_to_user_id' => User::factory(),
    ];
  }
}
