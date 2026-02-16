<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Circle;
use App\Models\Skill;
use App\Models\Achievement;
use App\Models\CircleMember;
use App\Models\Message;

class TrustCircleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $dams = User::factory()->create([
            'name' => 'Dams',
            'email' => 'dams@example.com',
            'password' => bcrypt('password'),
            'bio' => 'Founder of TrustCircle. Building communities through trust.',
            'location' => 'Colmar',
            'trust_score' => 95,
            'is_admin' => true,
        ]);

        $zak = User::factory()->create([
            'name' => 'Zak',
            'email' => 'zak@example.com',
            'password' => bcrypt('password'),
            'bio' => 'Professional Chef specialized in Japanese cuisine.',
            'location' => 'Colmar',
            'trust_score' => 88,
        ]);

        $users = User::factory(10)->create();

        // 2. Create Circles
        $restaurant = Circle::factory()->create([
            'name' => 'Restaurant de Dams',
            'type' => 'business',
            'description' => 'The best Japanese cuisine in Colmar. Verified quality only.',
            'address' => '12 Rue des Marchands, Colmar',
            'owner_id' => $dams->id,
        ]);

        $devCircle = Circle::factory()->create([
            'name' => 'God Stack Developers',
            'type' => 'project',
            'description' => 'A community of elite developers building portable apps.',
            'address' => 'Remote',
            'owner_id' => $dams->id,
        ]);

        // 3. Create Skills
        $sushiSkill = Skill::factory()->create(['name' => 'Sushi Master', 'category' => 'Cuisine']);
        $laravelSkill = Skill::factory()->create(['name' => 'Laravel Expert', 'category' => 'Dev']);

        // 4. Add Members & Achievements
        CircleMember::withoutEvents(function() use ($restaurant, $zak, $dams) {
            CircleMember::create([
                'circle_id' => $restaurant->id,
                'user_id' => $zak->id,
                'role' => 'member',
                'status' => 'active',
                'vouched_by_id' => $dams->id,
                'joined_at' => now(),
            ]);
        });

        Achievement::factory()->create([
            'user_id' => $zak->id,
            'skill_id' => $sushiSkill->id,
            'circle_id' => $restaurant->id,
            'title' => 'Sushi Saumon Avocat Signature',
            'description' => 'A masterwork of fresh salmon and creamy avocado.',
            'is_verified' => true,
        ]);

        Achievement::factory()->create([
            'user_id' => $dams->id,
            'skill_id' => $laravelSkill->id,
            'circle_id' => $devCircle->id,
            'title' => 'TrustCircle Core Architecture',
            'description' => 'Designed and implemented the core trust score algorithm.',
            'is_verified' => true,
        ]);

        // 5. Messages
        Message::create([
            'circle_id' => $devCircle->id,
            'sender_id' => $dams->id,
            'title' => 'Welcome to the circle',
            'content' => 'I can be there around 5h to discuss the roadmap.',
            'type' => 'chat',
            'metadata' => ['dispo' => '5h'],
        ]);
    }
}
