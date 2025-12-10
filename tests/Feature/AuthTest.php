<?php

namespace Tests\Feature;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_customer_registration_page_can_be_rendered(): void
    {
        $response = $this->get('/register/customer');
        $response->assertStatus(200);
    }

    public function test_provider_registration_page_can_be_rendered(): void
    {
        $response = $this->get('/register/provider');
        $response->assertStatus(200);
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register/customer', [
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '555-1234',
            'city' => 'Test City',
            'area' => 'Test Area',
            'address_details' => '123 Test St',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'testcustomer@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_provider_can_register(): void
    {
        $response = $this->post('/register/provider', [
            'name' => 'Test Provider',
            'email' => 'testprovider@example.com',
            'title' => 'Electrician',
            'phone' => '555-5678',
            'bio' => 'Test bio',
            'years_of_experience' => 5,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'testprovider@example.com',
            'role' => 'provider',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        CustomerProfile::create([
            'user_id' => $user->id,
            'phone' => '555-0000',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
