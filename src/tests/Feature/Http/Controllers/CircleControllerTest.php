<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Circle;
use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CircleController
 */
final class CircleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $circles = Circle::factory()->count(3)->create();

        $response = $this->get(route('circles.index'));

        $response->assertOk();
        $response->assertViewIs('circle.index');
        $response->assertViewHas('circles');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('circles.create'));

        $response->assertOk();
        $response->assertViewIs('circle.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CircleController::class,
            'store',
            \App\Http\Requests\CircleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $type = fake()->randomElement(/** enum_attributes **/);
        $address = fake()->word();
        $owner = Owner::factory()->create();
        $user = Owner::factory()->create();

        $response = $this->post(route('circles.store'), [
            'name' => $name,
            'type' => $type,
            'address' => $address,
            'owner_id' => $owner->id,
            'user_id' => $user->id,
        ]);

        $circles = Circle::query()
            ->where('name', $name)
            ->where('type', $type)
            ->where('address', $address)
            ->where('owner_id', $owner->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $circles);
        $circle = $circles->first();

        $response->assertRedirect(route('circles.index'));
        $response->assertSessionHas('circle.id', $circle->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $circle = Circle::factory()->create();

        $response = $this->get(route('circles.show', $circle));

        $response->assertOk();
        $response->assertViewIs('circle.show');
        $response->assertViewHas('circle');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $circle = Circle::factory()->create();

        $response = $this->get(route('circles.edit', $circle));

        $response->assertOk();
        $response->assertViewIs('circle.edit');
        $response->assertViewHas('circle');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CircleController::class,
            'update',
            \App\Http\Requests\CircleUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $circle = Circle::factory()->create();
        $name = fake()->name();
        $type = fake()->randomElement(/** enum_attributes **/);
        $address = fake()->word();
        $owner = Owner::factory()->create();
        $user = Owner::factory()->create();

        $response = $this->put(route('circles.update', $circle), [
            'name' => $name,
            'type' => $type,
            'address' => $address,
            'owner_id' => $owner->id,
            'user_id' => $user->id,
        ]);

        $circle->refresh();

        $response->assertRedirect(route('circles.index'));
        $response->assertSessionHas('circle.id', $circle->id);

        $this->assertEquals($name, $circle->name);
        $this->assertEquals($type, $circle->type);
        $this->assertEquals($address, $circle->address);
        $this->assertEquals($owner->id, $circle->owner_id);
        $this->assertEquals($user->id, $circle->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $circle = Circle::factory()->create();

        $response = $this->delete(route('circles.destroy', $circle));

        $response->assertRedirect(route('circles.index'));

        $this->assertModelMissing($circle);
    }
}
