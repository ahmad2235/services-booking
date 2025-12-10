<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Register a new customer.
     */
    public function registerCustomer(array $userData, array $profileData): User
    {
        $userData['password'] = Hash::make($userData['password']);
        
        return $this->userRepository->createCustomer($userData, $profileData);
    }

    /**
     * Register a new provider.
     */
    public function registerProvider(array $userData, array $profileData): User
    {
        $userData['password'] = Hash::make($userData['password']);
        
        return $this->userRepository->createProvider($userData, $profileData);
    }

    /**
     * Attempt to log in a user.
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Get the authenticated user.
     */
    public function user(): ?User
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated.
     */
    public function check(): bool
    {
        return Auth::check();
    }

    /**
     * Update user password.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
    }

    /**
     * Verify user password.
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
