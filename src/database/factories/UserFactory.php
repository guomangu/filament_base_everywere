<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar_url' => null,
            'bio' => fake()->sentence(),
            'location' => fake()->city(),
            'coordinates' => ['lat' => fake()->latitude, 'lng' => fake()->longitude],
            'trust_score' => 10,
            'is_admin' => false,
            'parent_id' => null,
            'is_managed' => false,
        ];
    }
}
