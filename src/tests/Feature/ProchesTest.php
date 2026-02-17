<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Skill;
use App\Models\Proche;
use App\Models\Achievement;
use App\Livewire\User\Profile;
use App\Livewire\User\Claim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProchesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_skill_to_themselves()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(Profile::class, ['user' => $user])
            ->set('skillName', 'Self Expert')
            ->call('submitSkillOnly');

        $this->assertDatabaseHas('skills', ['name' => 'Self Expert']);
        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'title' => '__SKELETON__',
            'proche_id' => null,
        ]);
    }

    public function test_user_can_create_a_proche()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(Profile::class, ['user' => $user])
            ->set('procheName', 'Petit Proche')
            ->call('createProche');

        $this->assertDatabaseHas('proches', [
            'name' => 'Petit Proche',
            'parent_id' => $user->id,
        ]);
    }

    public function test_parent_can_add_skill_to_proche()
    {
        $parent = User::factory()->create();
        $proche = Proche::create([
            'name' => 'Mon Petit',
            'parent_id' => $parent->id,
        ]);

        $this->actingAs($parent);

        Livewire::test(Profile::class, ['user' => $parent])
            ->call('openCreateModal', $proche->id)
            ->set('skillName', 'Poney')
            ->call('submitSkillOnly');

        $this->assertDatabaseHas('skills', ['name' => 'Poney']);
        $this->assertDatabaseHas('achievements', [
            'proche_id' => $proche->id,
            'user_id' => null,
            'title' => '__SKELETON__',
        ]);
    }

    public function test_parent_can_generate_transfer_code()
    {
        $parent = User::factory()->create();
        $proche = Proche::create([
            'name' => 'Mon Petit',
            'parent_id' => $parent->id,
        ]);

        $this->actingAs($parent);

        Livewire::test(Profile::class, ['user' => $parent])
            ->call('generateTransfer', $proche->id);

        $proche->refresh();
        $this->assertNotNull($proche->transfer_token);
        $this->assertNotNull($proche->transfer_code);
    }

    public function test_proche_can_claim_account_and_transfer_achievements()
    {
        $parent = User::factory()->create();
        $proche = Proche::create([
            'name' => 'Mon Petit',
            'parent_id' => $parent->id,
            'transfer_token' => 'test-token',
            'transfer_code' => 'ABCDEF',
        ]);

        $skill = Skill::create(['name' => 'Test', 'slug' => 'test']);
        Achievement::create([
            'proche_id' => $proche->id,
            'skill_id' => $skill->id,
            'title' => 'Mon Achievement',
            'description' => 'Test',
        ]);

        Livewire::test(Claim::class, ['token' => 'test-token'])
            ->set('code', 'ABCDEF')
            ->call('verifyCode')
            ->assertSet('verified', true)
            ->set('email', 'new@proche.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claimAccount')
            ->assertRedirect();

        // Verify User creation
        $this->assertDatabaseHas('users', [
            'name' => 'Mon Petit',
            'email' => 'new@proche.com',
            'parent_id' => $parent->id,
        ]);

        $user = User::where('email', 'new@proche.com')->first();

        // Verify Achievement transfer
        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'proche_id' => null,
            'title' => 'Mon Achievement',
        ]);

        // Verify Proche deletion
        $this->assertDatabaseMissing('proches', ['id' => $proche->id]);
    }
}
