<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CustomerProfile;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderTimeSlot;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $provider;
    protected CustomerProfile $customerProfile;
    protected ProviderProfile $providerProfile;
    protected ProviderService $providerService;
    protected ProviderTimeSlot $timeSlot;

    protected function setUp(): void
    {
        parent::setUp();

        // Create customer
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);
        $this->customerProfile = CustomerProfile::create([
            'user_id' => $this->customer->id,
            'phone' => '555-0001',
            'address' => '123 Test St',
        ]);

        // Create provider
        $this->provider = User::factory()->create([
            'role' => 'provider',
            'is_active' => true,
        ]);
        $this->providerProfile = ProviderProfile::create([
            'user_id' => $this->provider->id,
            'company_name' => 'Test Company',
            'phone' => '555-0002',
        ]);

        // Create service
        $category = ServiceCategory::create(['name' => 'Test Category']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Test Service',
            'base_price' => 100,
            'duration_minutes' => 60,
        ]);

        $this->providerService = ProviderService::create([
            'provider_profile_id' => $this->providerProfile->id,
            'service_id' => $service->id,
            'price' => 100,
            'is_active' => true,
        ]);

        // Create time slot
        $this->timeSlot = ProviderTimeSlot::create([
            'provider_profile_id' => $this->providerProfile->id,
            'start_datetime' => now()->addDays(1)->setTime(9, 0),
            'end_datetime' => now()->addDays(1)->setTime(17, 0),
            'status' => 'available',
        ]);
    }

    public function test_customer_can_view_booking_form(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('customer.bookings.create', $this->providerService->id));

        $response->assertStatus(200);
        $response->assertSee('Book Service');
    }

    public function test_customer_can_create_booking(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), [
                'provider_service_id' => $this->providerService->id,
                'time_slot_id' => $this->timeSlot->id,
                'address' => '456 New Address',
                'notes' => 'Test notes',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'provider_service_id' => $this->providerService->id,
            'status' => 'pending',
        ]);
    }

    public function test_customer_can_view_their_bookings(): void
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'provider_profile_id' => $this->providerProfile->id,
            'provider_service_id' => $this->providerService->id,
            'time_slot_id' => $this->timeSlot->id,
            'scheduled_at' => now()->addDays(1),
            'status' => 'pending',
            'total_price' => 100,
            'address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('customer.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Service');
    }

    public function test_customer_can_cancel_pending_booking(): void
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'provider_profile_id' => $this->providerProfile->id,
            'provider_service_id' => $this->providerService->id,
            'time_slot_id' => $this->timeSlot->id,
            'scheduled_at' => now()->addDays(2),
            'status' => 'pending',
            'total_price' => 100,
            'address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.cancel', $booking->id), [
                'cancellation_reason' => 'Changed my mind',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_provider_can_accept_booking(): void
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'provider_profile_id' => $this->providerProfile->id,
            'provider_service_id' => $this->providerService->id,
            'time_slot_id' => $this->timeSlot->id,
            'scheduled_at' => now()->addDays(1),
            'status' => 'pending',
            'total_price' => 100,
            'address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->provider)
            ->post(route('provider.bookings.accept', $booking->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_provider_can_reject_booking(): void
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'provider_profile_id' => $this->providerProfile->id,
            'provider_service_id' => $this->providerService->id,
            'time_slot_id' => $this->timeSlot->id,
            'scheduled_at' => now()->addDays(1),
            'status' => 'pending',
            'total_price' => 100,
            'address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->provider)
            ->post(route('provider.bookings.reject', $booking->id), [
                'rejection_reason' => 'Not available',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'rejected',
        ]);
    }

    public function test_provider_can_complete_booking(): void
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'provider_profile_id' => $this->providerProfile->id,
            'provider_service_id' => $this->providerService->id,
            'time_slot_id' => $this->timeSlot->id,
            'scheduled_at' => now()->addDays(1),
            'status' => 'confirmed',
            'total_price' => 100,
            'address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->provider)
            ->post(route('provider.bookings.complete', $booking->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'completed',
        ]);
    }
}
