<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_can_view_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_user_details(): void
    {
        $user = User::factory()->create([ 'role' => 'customer', 'is_active' => true ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_admin_services_filters_by_category(): void
    {
        $categoryA = \App\Models\ServiceCategory::create(['name' => 'A']);
        $categoryB = \App\Models\ServiceCategory::create(['name' => 'B']);

        \App\Models\Service::create(['category_id' => $categoryA->id, 'name' => 'Service A', 'base_price' => 10, 'duration_minutes' => 30]);
        \App\Models\Service::create(['category_id' => $categoryB->id, 'name' => 'Service B', 'base_price' => 20, 'duration_minutes' => 60]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.services.index', ['category_id' => $categoryA->id]));

        $response->assertStatus(200);
        $response->assertSee('Service A');
        $response->assertDontSee('Service B');
    }

    public function test_admin_services_search(): void
    {
        $category = \App\Models\ServiceCategory::create(['name' => 'SearchCat']);
        $service1 = \App\Models\Service::create(['category_id' => $category->id, 'name' => 'Unique Name 123', 'base_price' => 15, 'duration_minutes' => 45]);
        $service2 = \App\Models\Service::create(['category_id' => $category->id, 'name' => 'Regular Service', 'base_price' => 25, 'duration_minutes' => 60]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.services.index', ['search' => 'Unique Name']));

        $response->assertStatus(200);
        $response->assertSee('Unique Name 123');
        $response->assertDontSee('Regular Service');
    }

    public function test_admin_can_hide_rating(): void
    {
        $providerUser = \App\Models\User::factory()->provider()->create();
        $providerProfile = \App\Models\ProviderProfile::create([ 'user_id' => $providerUser->id ]);
        $category = \App\Models\ServiceCategory::create(['name' => 'RatingsCat']);
        $service = \App\Models\Service::create(['category_id' => $category->id, 'name' => 'Rating Service', 'base_price' => 120, 'duration_minutes' => 60]);
        $providerService = \App\Models\ProviderService::create(['provider_profile_id' => $providerProfile->id, 'service_id' => $service->id, 'price' => 100]);
        $timeSlot = \App\Models\ProviderTimeSlot::create(['provider_profile_id' => $providerProfile->id, 'start_datetime' => now()->addDay(), 'end_datetime' => now()->addDay()->addHour(), 'status' => 'available']);
        $customerUser = \App\Models\User::factory()->customer()->create();
        $customerProfile = \App\Models\CustomerProfile::create(['user_id' => $customerUser->id]);
        $booking = \App\Models\Booking::create([
            'customer_id' => $customerUser->id,
            'provider_profile_id' => $providerProfile->id,
            'provider_service_id' => $providerService->id,
            'time_slot_id' => $timeSlot->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'completed',
            'total_price' => $providerService->price,
        ]);
        $rating = \App\Models\Rating::create([
            'booking_id' => $booking->id,
            'provider_profile_id' => $providerProfile->id,
            'rating_value' => 5,
            'comment' => 'Great job',
            'is_visible' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.ratings.hide', $rating->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'is_visible' => false,
            'hidden_by_admin_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.toggle-active', $user->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'description' => 'Test Description',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('service_categories', [
            'name' => 'Test Category',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category->id), [
                'name' => 'New Name',
                'description' => 'New Description',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('service_categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = ServiceCategory::create([
            'name' => 'To Delete',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('service_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        /** @var \App\Models\User $customer */
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
