<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Circle;
use App\Models\Message;
use App\Models\User;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'sender_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement(["chat","logistics"]),
            'metadata' => ['dispo' => '5h'],
        ];
    }
}
