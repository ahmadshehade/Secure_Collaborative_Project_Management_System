<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Summary of model
     * @var 
     */
    protected $model = Comment::class;

    /**
     * Summary of definition
     * @return array{commentable_id: int, commentable_type: string, content: string, user_id: UserFactory}
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentence,
            'user_id' => \App\Models\User::factory(),
            'commentable_type' => \App\Models\Project::class,
            'commentable_id' => 1,
        ];
    }
}
