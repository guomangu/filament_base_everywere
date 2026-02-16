<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Circle;
use App\Models\User;

class CircleFactory extends Factory
{
    protected $model = Circle::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->text(),
            'type' => fake()->randomElement(["business","event","place","project"]),
            'address' => fake()->address(),
            'coordinates' => ['lat' => fake()->latitude(), 'lng' => fake()->longitude()],
            'owner_id' => User::factory(),
        ];
    }
}
