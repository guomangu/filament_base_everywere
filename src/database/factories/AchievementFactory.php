<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Achievement;
use App\Models\Circle;
use App\Models\Skill;
use App\Models\User;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'skill_id' => Skill::factory(),
            'circle_id' => Circle::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'media_url' => fake()->word(),
            'metadata' => '{}',
            'is_verified' => fake()->boolean(),
        ];
    }
}
