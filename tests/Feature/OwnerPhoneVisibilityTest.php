<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPhoneVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_phone_is_hidden_from_public_property_page(): void
    {
        $property = Property::factory()->create([
            'title' => 'Тестовая квартира',
            'slug' => 'test-owner-phone-listing',
            'owner_phone' => '+79990001122',
            'phone_override' => '+78880001122',
            'is_published' => true,
        ]);

        $this->get(route('properties.show', $property))
            ->assertOk()
            ->assertDontSee('+79990001122', false)
            ->assertSee('+78880001122');
    }

    public function test_owner_phone_is_excluded_from_model_serialization(): void
    {
        $property = Property::factory()->create([
            'owner_phone' => '+79990001122',
        ]);

        $this->assertArrayNotHasKey('owner_phone', $property->toArray());
        $this->assertStringNotContainsString('+79990001122', $property->toJson());
        $this->assertSame('+79990001122', $property->owner_phone);
    }

    public function test_staff_can_see_owner_phone_on_admin_form(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create([
            'owner_phone' => '+79990001122',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.properties.edit', $property))
            ->assertOk()
            ->assertSee('Телефон собственника')
            ->assertSee('+79990001122');
    }
}
