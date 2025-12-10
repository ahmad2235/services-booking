<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\User;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_filter_returns_matching_providers()
    {
        $categoryA = ServiceCategory::create(['name' => 'Plumbing', 'is_active' => true]);
        $categoryB = ServiceCategory::create(['name' => 'Cleaning', 'is_active' => true]);

        $serviceA = Service::create(['name' => 'Pipe Repair', 'category_id' => $categoryA->id, 'is_active' => true]);
        $serviceB = Service::create(['name' => 'Home Cleaning', 'category_id' => $categoryB->id, 'is_active' => true]);

        // Provider for Plumbing
        $userA = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profileA = new ProviderProfile();
        $profileA->user_id = $userA->id;
        $profileA->company_name = 'Joe Plumbing';
        $profileA->min_price = 50;
        $profileA->max_price = 250;
        $profileA->avg_rating = 4.5;
        $profileA->save();

        ProviderService::create(['provider_profile_id' => $profileA->id, 'service_id' => $serviceA->id, 'price' => 75, 'is_active' => true]);

        // Provider for Cleaning
        $userB = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profileB = new ProviderProfile();
        $profileB->user_id = $userB->id;
        $profileB->company_name = 'Sparkle Clean Co.';
        $profileB->min_price = 30;
        $profileB->max_price = 120;
        $profileB->avg_rating = 4.0;
        $profileB->save();

        ProviderService::create(['provider_profile_id' => $profileB->id, 'service_id' => $serviceB->id, 'price' => 50, 'is_active' => true]);

        // Filter by Plumbing category
        $response = $this->get('/services?category_id=' . $categoryA->id);
        $response->assertStatus(200);
        $response->assertSeeText('Joe Plumbing');
        $response->assertDontSeeText('Sparkle Clean Co.');
    }

    public function test_search_finds_provider_by_service_or_company()
    {
        // Setup reused from above
        $category = ServiceCategory::create(['name' => 'Plumbing', 'is_active' => true]);
        $service = Service::create(['name' => 'Pipe Repair', 'category_id' => $category->id, 'is_active' => true]);

        $user = User::factory()->create(['role' => 'provider', 'is_active' => true]);
        $profile = new ProviderProfile();
        $profile->user_id = $user->id;
        $profile->company_name = 'Fix-It Plumbers';
        $profile->avg_rating = 4.6;
        $profile->save();

        ProviderService::create(['provider_profile_id' => $profile->id, 'service_id' => $service->id, 'price' => 99, 'is_active' => true]);

        $response = $this->get('/services?search=Fix-It');
        $response->assertStatus(200);
        $response->assertSeeText('Fix-It Plumbers');

        // search by service name
        $response2 = $this->get('/services?search=Pipe');
        $response2->assertStatus(200);
        $response2->assertSeeText('Fix-It Plumbers');
    }
}
