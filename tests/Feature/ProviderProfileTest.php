<?php

namespace Tests\Feature;

use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_view_profile_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profile = ProviderProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Co',
            'phone' => '555-9999',
            'bio' => 'Test bio',
            'years_of_experience' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('provider.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Edit Profile');
        $response->assertSee($profile->company_name);
    }

    public function test_provider_can_view_locations_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profile = ProviderProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Co',
            'phone' => '555-9999',
            'bio' => 'Test bio',
            'years_of_experience' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('provider.locations.edit'));
        $response->assertStatus(200);
        $response->assertSee('Service Areas');
    }

    public function test_provider_can_update_locations(): void
    {
        $user = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profile = ProviderProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Co',
            'phone' => '555-9999',
            'bio' => 'Test bio',
            'years_of_experience' => 2,
        ]);

        // Create locations via DB directly
        \DB::table('locations')->insert([
            ['city' => 'CityA', 'state' => 'ST', 'zip_code' => '00001', 'country' => 'USA', 'created_at' => now(), 'updated_at' => now()],
            ['city' => 'CityB', 'state' => 'ST', 'zip_code' => '00002', 'country' => 'USA', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $locations = \App\Models\Location::all();

        $response = $this->actingAs($user)->put(route('provider.locations.update'), [
            'location_ids' => [$locations[0]->id, $locations[1]->id],
        ]);

        $response->assertRedirect(route('provider.profile.edit'));
        $this->assertDatabaseHas('provider_profile_location', ['provider_profile_id' => $profile->id, 'location_id' => $locations[0]->id]);
    }

    public function test_admin_cannot_view_provider_profile_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('provider.profile.edit'));
        $response->assertStatus(403);
    }
}
