<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Circle;
use App\Models\CircleMember;
use App\Models\User;

class CircleMemberFactory extends Factory
{
    protected $model = CircleMember::class;

    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement(["admin","member","guest"]),
            'status' => fake()->randomElement(["pending","active","rejected"]),
            'vouched_by_id' => User::factory(),
            'joined_at' => now(),
        ];
    }
}
