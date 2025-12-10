<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\RatingController as CustomerRatingController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\ProfileController as ProviderProfileController;
use App\Http\Controllers\Provider\ServiceController as ProviderServiceController;
use App\Http\Controllers\Provider\TimeSlotController as ProviderTimeSlotController;
use App\Http\Controllers\Provider\BookingController as ProviderBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\RatingController as AdminRatingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/providers/{provider}', [HomeController::class, 'showProvider'])->name('providers.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register/customer', [AuthController::class, 'showRegisterCustomerForm'])->name('register.customer');
    Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
    Route::get('/register/provider', [AuthController::class, 'showRegisterProviderForm'])->name('register.provider');
    Route::post('/register/provider', [AuthController::class, 'registerProvider']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Customer Routes
Route::middleware(['auth', 'active', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{providerService?}', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Ratings
    Route::get('/bookings/{booking}/rate', [CustomerRatingController::class, 'create'])->name('ratings.create');
    Route::post('/bookings/{booking}/rate', [CustomerRatingController::class, 'store'])->name('ratings.store');
});

// Provider Routes
Route::middleware(['auth', 'active', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProviderProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
    Route::get('/locations', [ProviderProfileController::class, 'editLocations'])->name('locations.edit');
    Route::put('/locations', [ProviderProfileController::class, 'updateLocations'])->name('locations.update');
    
    // Services
    Route::get('/services', [ProviderServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ProviderServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ProviderServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{providerService}/edit', [ProviderServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{providerService}', [ProviderServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{providerService}', [ProviderServiceController::class, 'destroy'])->name('services.destroy');
    
    // Time Slots
    Route::get('/time-slots', [ProviderTimeSlotController::class, 'index'])->name('time-slots.index');
    Route::get('/time-slots/create', [ProviderTimeSlotController::class, 'create'])->name('time-slots.create');
    Route::post('/time-slots', [ProviderTimeSlotController::class, 'store'])->name('time-slots.store');
    Route::get('/time-slots/{timeSlot}/edit', [ProviderTimeSlotController::class, 'edit'])->name('time-slots.edit');
    Route::put('/time-slots/{timeSlot}', [ProviderTimeSlotController::class, 'update'])->name('time-slots.update');
    Route::delete('/time-slots/{timeSlot}', [ProviderTimeSlotController::class, 'destroy'])->name('time-slots.destroy');
    
    // Bookings
    Route::get('/bookings', [ProviderBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [ProviderBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/accept', [ProviderBookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking}/reject', [ProviderBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/complete', [ProviderBookingController::class, 'complete'])->name('bookings.complete');
});

// Admin Routes
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
    
    // Service Categories
    Route::resource('categories', AdminServiceCategoryController::class);
    
    // Services
    Route::resource('services', AdminServiceController::class);
    
    // Ratings
    Route::get('/ratings', [AdminRatingController::class, 'index'])->name('ratings.index');
    Route::post('/ratings/{rating}/hide', [AdminRatingController::class, 'hide'])->name('ratings.hide');
});
