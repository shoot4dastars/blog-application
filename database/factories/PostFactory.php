<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => \Str::slug($title),
            'body' => fake()->paragraphs(5, true),
            'user_id' => User::factory(),
            'view_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
