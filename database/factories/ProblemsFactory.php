<?php

namespace Database\Factories;

use App\Models\Problem;
use App\Models\Problems;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemsFactory extends Factory
{
    protected $model = Problems::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['bug', 'feature', 'other']), // Adjust types as needed
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(), // generates a related user
            'department' => 'dadam', // change as needed
            'approved' => false, // default
        ];
    }
}
