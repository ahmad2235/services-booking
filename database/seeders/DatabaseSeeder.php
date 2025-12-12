<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\CustomerProfile;
use App\Models\Location;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderTimeSlot;
use App\Models\Rating;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@homeservices.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Locations
        $locations = [
            ['city' => 'New York', 'state' => 'NY', 'zip_code' => '10001', 'country' => 'USA'],
            ['city' => 'Los Angeles', 'state' => 'CA', 'zip_code' => '90001', 'country' => 'USA'],
            ['city' => 'Chicago', 'state' => 'IL', 'zip_code' => '60601', 'country' => 'USA'],
            ['city' => 'Houston', 'state' => 'TX', 'zip_code' => '77001', 'country' => 'USA'],
            ['city' => 'Phoenix', 'state' => 'AZ', 'zip_code' => '85001', 'country' => 'USA'],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        // Create Service Categories
        $categories = [
            ['name' => 'Plumbing', 'description' => 'All plumbing services including repairs, installations, and maintenance.'],
            ['name' => 'Electrical', 'description' => 'Electrical repairs, installations, and safety inspections.'],
            ['name' => 'Cleaning', 'description' => 'Home cleaning, deep cleaning, and specialized cleaning services.'],
            ['name' => 'HVAC', 'description' => 'Heating, ventilation, and air conditioning services.'],
            ['name' => 'Landscaping', 'description' => 'Lawn care, gardening, and outdoor maintenance.'],
        ];

        foreach ($categories as $categoryData) {
            ServiceCategory::create($categoryData);
        }

        // Create Services
        $services = [
            // Plumbing
            ['category_id' => 1, 'name' => 'Leak Repair', 'description' => 'Fix leaking pipes and faucets', 'base_price' => 75.00, 'duration_minutes' => 60],
            ['category_id' => 1, 'name' => 'Drain Cleaning', 'description' => 'Clear clogged drains and pipes', 'base_price' => 100.00, 'duration_minutes' => 45],
            ['category_id' => 1, 'name' => 'Water Heater Installation', 'description' => 'Install new water heater', 'base_price' => 300.00, 'duration_minutes' => 180],
            // Electrical
            ['category_id' => 2, 'name' => 'Outlet Installation', 'description' => 'Install new electrical outlets', 'base_price' => 80.00, 'duration_minutes' => 45],
            ['category_id' => 2, 'name' => 'Light Fixture Installation', 'description' => 'Install ceiling lights and fixtures', 'base_price' => 60.00, 'duration_minutes' => 30],
            ['category_id' => 2, 'name' => 'Electrical Inspection', 'description' => 'Complete home electrical safety inspection', 'base_price' => 150.00, 'duration_minutes' => 90],
            // Cleaning
            ['category_id' => 3, 'name' => 'Regular House Cleaning', 'description' => 'Standard cleaning service', 'base_price' => 120.00, 'duration_minutes' => 120],
            ['category_id' => 3, 'name' => 'Deep Cleaning', 'description' => 'Thorough deep cleaning service', 'base_price' => 200.00, 'duration_minutes' => 240],
            ['category_id' => 3, 'name' => 'Move-out Cleaning', 'description' => 'Complete cleaning for moving out', 'base_price' => 250.00, 'duration_minutes' => 300],
            // HVAC
            ['category_id' => 4, 'name' => 'AC Repair', 'description' => 'Air conditioner repair service', 'base_price' => 150.00, 'duration_minutes' => 90],
            ['category_id' => 4, 'name' => 'Furnace Maintenance', 'description' => 'Annual furnace maintenance', 'base_price' => 100.00, 'duration_minutes' => 60],
            // Landscaping
            ['category_id' => 5, 'name' => 'Lawn Mowing', 'description' => 'Regular lawn mowing service', 'base_price' => 50.00, 'duration_minutes' => 60],
            ['category_id' => 5, 'name' => 'Garden Maintenance', 'description' => 'Complete garden care', 'base_price' => 100.00, 'duration_minutes' => 120],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }

        // Create Customer Users
        $customers = [
            ['name' => 'John Customer', 'email' => 'customer1@test.com', 'phone' => '555-0101', 'address' => '123 Main St, New York, NY 10001'],
            ['name' => 'Jane Doe', 'email' => 'customer2@test.com', 'phone' => '555-0102', 'address' => '456 Oak Ave, Los Angeles, CA 90001'],
            ['name' => 'Bob Smith', 'email' => 'customer3@test.com', 'phone' => '555-0103', 'address' => '789 Pine Rd, Chicago, IL 60601'],
        ];

        $customerProfiles = [];
        foreach ($customers as $customerData) {
            $user = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]);

            $customerProfiles[] = CustomerProfile::create([
                'user_id' => $user->id,
                'phone' => $customerData['phone'],
                'address' => $customerData['address'],
            ]);
        }

        // Create Provider Users
        $providers = [
            [
                'name' => 'Mike Plumber',
                'email' => 'provider1@test.com',
                'company_name' => 'Mike\'s Plumbing Services',
                'phone' => '555-0201',
                'bio' => 'Licensed plumber with over 10 years of experience. Specializing in residential plumbing repairs and installations.',
                'years_of_experience' => 10,
                'services' => [1, 2, 3], // Plumbing services
                'locations' => [1, 2], // NY, LA
            ],
            [
                'name' => 'Sarah Electric',
                'email' => 'provider2@test.com',
                'company_name' => 'Bright Spark Electrical',
                'phone' => '555-0202',
                'bio' => 'Certified electrician providing safe and reliable electrical services for homes and small businesses.',
                'years_of_experience' => 8,
                'services' => [4, 5, 6], // Electrical services
                'locations' => [1, 3], // NY, Chicago
            ],
            [
                'name' => 'Clean Team',
                'email' => 'provider3@test.com',
                'company_name' => 'Sparkle Clean Co.',
                'phone' => '555-0203',
                'bio' => 'Professional cleaning team dedicated to making your home spotless. Eco-friendly products available.',
                'years_of_experience' => 5,
                'services' => [7, 8, 9], // Cleaning services
                'locations' => [1, 2, 3, 4], // Multiple locations
            ],
        ];

        $providerProfiles = [];
        foreach ($providers as $providerData) {
            $user = User::create([
                'name' => $providerData['name'],
                'email' => $providerData['email'],
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
            ]);

            $profile = ProviderProfile::create([
                'user_id' => $user->id,
                'company_name' => $providerData['company_name'],
                'phone' => $providerData['phone'],
                'bio' => $providerData['bio'],
                'years_of_experience' => $providerData['years_of_experience'],
                'avg_rating' => 0,
            ]);

            $providerProfiles[] = $profile;

            // Assign services to provider
            foreach ($providerData['services'] as $serviceId) {
                $service = Service::find($serviceId);
                ProviderService::create([
                    'provider_profile_id' => $profile->id,
                    'service_id' => $serviceId,
                    'price' => $service->base_price * (1 + rand(-10, 20) / 100), // Slight price variation
                    'is_active' => true,
                ]);
            }

            // Assign locations to provider
            $profile->locations()->attach($providerData['locations']);

            // Create time slots for next 7 days
            for ($day = 0; $day < 7; $day++) {
                $date = now()->addDays($day)->setTime(9, 0, 0);
                
                // Morning slot
                ProviderTimeSlot::create([
                    'provider_profile_id' => $profile->id,
                    'start_datetime' => $date->copy(),
                    'end_datetime' => $date->copy()->addHours(4),
                    'status' => 'available',
                ]);

                // Afternoon slot
                ProviderTimeSlot::create([
                    'provider_profile_id' => $profile->id,
                    'start_datetime' => $date->copy()->setTime(14, 0, 0),
                    'end_datetime' => $date->copy()->setTime(18, 0, 0),
                    'status' => 'available',
                ]);
            }
        }

        // Create sample bookings
        $providerService1 = ProviderService::where('provider_profile_id', $providerProfiles[0]->id)->first();
        $providerService2 = ProviderService::where('provider_profile_id', $providerProfiles[1]->id)->first();
        $providerService3 = ProviderService::where('provider_profile_id', $providerProfiles[2]->id)->first();

        // Completed booking with rating
        $booking1 = Booking::create([
            'customer_id' => $customerProfiles[0]->id,
            'provider_profile_id' => $providerProfiles[0]->id,
            'provider_service_id' => $providerService1->id,
            'time_slot_id' => ProviderTimeSlot::where('provider_profile_id', $providerProfiles[0]->id)->first()->id,
            'scheduled_at' => now()->subDays(5),
            'status' => 'completed',
            'total_price' => $providerService1->price,
            'address' => $customerProfiles[0]->address,
            'notes' => 'Please call when arriving.',
        ]);

        Rating::create([
            'booking_id' => $booking1->id,
            'provider_profile_id' => $booking1->provider_profile_id,
            'rating_value' => 5,
            'comment' => 'Excellent service! Mike was professional and fixed our leak quickly.',
            'is_visible' => true,
        ]);

        // Update provider rating
        $providerProfiles[0]->update(['avg_rating' => 5.0]);

        // Pending booking
        Booking::create([
            'customer_id' => $customerProfiles[1]->id,
            'provider_profile_id' => $providerProfiles[1]->id,
            'provider_service_id' => $providerService2->id,
            'time_slot_id' => ProviderTimeSlot::where('provider_profile_id', $providerProfiles[1]->id)->where('status', 'available')->first()->id,
            'scheduled_at' => now()->addDays(2),
            'status' => 'pending',
            'total_price' => $providerService2->price,
            'address' => $customerProfiles[1]->address,
        ]);

        // Confirmed booking
        Booking::create([
            'customer_id' => $customerProfiles[2]->id,
            'provider_profile_id' => $providerProfiles[2]->id,
            'provider_service_id' => $providerService3->id,
            'time_slot_id' => ProviderTimeSlot::where('provider_profile_id', $providerProfiles[2]->id)->where('status', 'available')->skip(1)->first()->id,
            'scheduled_at' => now()->addDays(3),
            'status' => 'confirmed',
            'total_price' => $providerService3->price,
            'address' => $customerProfiles[2]->address,
            'notes' => 'I have a dog, please be aware.',
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@homeservices.com / password');
        $this->command->info('Customer: customer1@test.com / password');
        $this->command->info('Provider: provider1@test.com / password');
    }
}
