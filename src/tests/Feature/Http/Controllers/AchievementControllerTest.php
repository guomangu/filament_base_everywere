<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Achievement;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AchievementController
 */
final class AchievementControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $achievements = Achievement::factory()->count(3)->create();

        $response = $this->get(route('achievements.index'));

        $response->assertOk();
        $response->assertViewIs('achievement.index');
        $response->assertViewHas('achievements');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('achievements.create'));

        $response->assertOk();
        $response->assertViewIs('achievement.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AchievementController::class,
            'store',
            \App\Http\Requests\AchievementStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create();
        $title = fake()->sentence(4);
        $is_verified = fake()->boolean();

        $response = $this->post(route('achievements.store'), [
            'user_id' => $user->id,
            'skill_id' => $skill->id,
            'title' => $title,
            'is_verified' => $is_verified,
        ]);

        $achievements = Achievement::query()
            ->where('user_id', $user->id)
            ->where('skill_id', $skill->id)
            ->where('title', $title)
            ->where('is_verified', $is_verified)
            ->get();
        $this->assertCount(1, $achievements);
        $achievement = $achievements->first();

        $response->assertRedirect(route('achievements.index'));
        $response->assertSessionHas('achievement.id', $achievement->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $achievement = Achievement::factory()->create();

        $response = $this->get(route('achievements.show', $achievement));

        $response->assertOk();
        $response->assertViewIs('achievement.show');
        $response->assertViewHas('achievement');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $achievement = Achievement::factory()->create();

        $response = $this->get(route('achievements.edit', $achievement));

        $response->assertOk();
        $response->assertViewIs('achievement.edit');
        $response->assertViewHas('achievement');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AchievementController::class,
            'update',
            \App\Http\Requests\AchievementUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $achievement = Achievement::factory()->create();
        $user = User::factory()->create();
        $skill = Skill::factory()->create();
        $title = fake()->sentence(4);
        $is_verified = fake()->boolean();

        $response = $this->put(route('achievements.update', $achievement), [
            'user_id' => $user->id,
            'skill_id' => $skill->id,
            'title' => $title,
            'is_verified' => $is_verified,
        ]);

        $achievement->refresh();

        $response->assertRedirect(route('achievements.index'));
        $response->assertSessionHas('achievement.id', $achievement->id);

        $this->assertEquals($user->id, $achievement->user_id);
        $this->assertEquals($skill->id, $achievement->skill_id);
        $this->assertEquals($title, $achievement->title);
        $this->assertEquals($is_verified, $achievement->is_verified);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $achievement = Achievement::factory()->create();

        $response = $this->delete(route('achievements.destroy', $achievement));

        $response->assertRedirect(route('achievements.index'));

        $this->assertModelMissing($achievement);
    }
}
