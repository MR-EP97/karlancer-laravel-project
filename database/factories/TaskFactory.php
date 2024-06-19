<?php

namespace Database\Factories;

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
        $statuses = ['pending', 'in_progress', 'completed'];

        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'user_id' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
