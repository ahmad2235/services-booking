<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the booking system.
    |
    */

    // Hours before scheduled time when cancellation is no longer allowed
    'cancel_hours_before' => env('BOOKING_CANCEL_HOURS_BEFORE', 24),

    // Minimum booking duration in minutes
    'min_duration_minutes' => 30,

    // Maximum booking duration in minutes
    'max_duration_minutes' => 480, // 8 hours

    // Booking statuses
    'statuses' => [
        'pending' => 'pending',
        'confirmed' => 'confirmed',
        'rejected' => 'rejected',
        'cancelled' => 'cancelled',
        'completed' => 'completed',
    ],

    // Time slot statuses
    'time_slot_statuses' => [
        'available' => 'available',
        'reserved' => 'reserved',
        'blocked' => 'blocked',
    ],

    // User roles
    'roles' => [
        'customer' => 'customer',
        'provider' => 'provider',
        'admin' => 'admin',
    ],

    // Rating configuration
    'rating' => [
        'min' => 1,
        'max' => 5,
    ],

    // Pagination
    'pagination' => [
        'users' => 15,
        'bookings' => 10,
        'services' => 12,
        'providers' => 12,
        'ratings' => 10,
    ],

];
