<?php

namespace Tests\Feature;

use App\Models\ProviderProfile;
use App\Models\ProviderTimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderTimeSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_see_time_slots_index(): void
    {
        // Create provider user and profile
        $user = User::factory()->create([
            'role' => 'provider',
            'is_active' => true,
        ]);

        $profile = ProviderProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Co',
            'phone' => '555-9999',
            'bio' => 'Test provider',
            'years_of_experience' => 3,
        ]);

        // Create a time slot for the provider
        $slot = ProviderTimeSlot::create([
            'provider_profile_id' => $profile->id,
            'start_datetime' => now()->setTime(9, 0, 0),
            'end_datetime' => now()->setTime(12, 0, 0),
            'status' => 'available',
        ]);

        $response = $this->actingAs($user)->get(route('provider.time-slots.index'));

        $response->assertStatus(200);
        $response->assertSee('Time Slots');
        $response->assertSee((string) $slot->id);
    }
}
