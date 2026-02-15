<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main user
        $guuu = \App\Models\User::firstOrCreate(
            ['email' => 'guuu@example.com'],
            [
                'name' => 'Guuu',
                'password' => bcrypt('password'),
            ]
        );

        // Create other users
        $users = \App\Models\User::factory(10)->create();

        // Create posts
        $users->push($guuu)->each(function ($user) {
            \App\Models\Post::factory(rand(1, 5))->create([
                'user_id' => $user->id,
            ]);
        });

        // Add random reactions
        \App\Models\Post::all()->each(function ($post) use ($users) {
            foreach ($users->random(rand(0, 5)) as $user) {
                $post->reactions()->create([
                    'user_id' => $user->id,
                    'type' => collect(['like', 'love', 'haha'])->random(),
                ]);
            }
        });
    }
}
