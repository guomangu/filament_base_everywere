<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GodStackDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Skills
        $skills = [
            ['name' => 'Laravel Development', 'slug' => 'laravel', 'category' => 'Code'],
            ['name' => 'AI Engineering', 'slug' => 'ai', 'category' => 'Intelligence'],
            ['name' => 'Social Architecture', 'slug' => 'social', 'category' => 'Human'],
            ['name' => 'Portable Systems', 'slug' => 'portable', 'category' => 'Infra'],
        ];

        foreach ($skills as $skill) {
            \App\Models\Skill::updateOrCreate(['slug' => $skill['slug']], $skill);
        }

        // 2. Main User
        $guillaume = \App\Models\User::where('email', 'gyomang@gmail.com')->first();
        if (!$guillaume) {
            $guillaume = \App\Models\User::factory()->create([
                'name' => 'Guillaume',
                'email' => 'gyomang@gmail.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 3. Circles
        $circles = [
            [
                'name' => 'The Forge',
                'description' => 'A place to build portable stacks and desktop-first web apps.',
                'type' => 'project',
                'address' => 'Digital Space #101',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'is_public' => true,
                'owner_id' => $guillaume->id,
            ],
            [
                'name' => 'AI Guild',
                'description' => 'Researchers and engineers pushing the boundaries of agentic coding.',
                'type' => 'event',
                'address' => 'Silicon Valley (Remote)',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
                'is_public' => true,
                'owner_id' => $guillaume->id,
            ],
        ];

        foreach ($circles as $circleData) {
            $circle = \App\Models\Circle::updateOrCreate(['name' => $circleData['name']], $circleData);
            
            // Add member
            $circle->members()->create([
                'user_id' => $guillaume->id,
                'role' => 'admin',
                'status' => 'active',
                'joined_at' => now(),
            ]);
        }

        // 4. Achievements
        $skillId = \App\Models\Skill::where('slug', 'laravel')->first()->id;
        $guillaume->achievements()->create([
            'skill_id' => $skillId,
            'title' => 'God Stack Master',
            'description' => 'Successfully deployed a portable Laravel stack with MariaDB and FrankenPHP in a single binary environment.',
            'is_verified' => true,
        ]);
    }
}
