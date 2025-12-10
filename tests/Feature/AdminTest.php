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
