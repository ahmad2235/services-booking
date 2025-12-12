<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

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
        try {
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();

                // Check if user is active
                if (!$user->is_active) {
                    Auth::logout();
                    return false;
                }

                return true;
            }
        } catch (RuntimeException $e) {
            // Hash driver mismatch (e.g. bcrypt vs argon hashed value). Try fallback check with other drivers
            $email = $credentials['email'] ?? null;
            $plain = $credentials['password'] ?? null;

            if ($email && $plain) {
                $user = $this->userRepository->findByEmail($email);
                if ($user) {
                    $drivers = ['argon2id', 'argon', 'bcrypt'];
                    foreach ($drivers as $driver) {
                        try {
                            if (Hash::driver($driver)->check($plain, $user->password)) {
                                // Re-hash the password using the current default driver and login the user
                                $user->password = Hash::make($plain);
                                $user->save();
                                Auth::loginUsingId($user->id, $remember);

                                if (!$user->is_active) {
                                    Auth::logout();
                                    return false;
                                }

                                return true;
                            }
                        } catch (RuntimeException $ex) {
                            // driver couldn't check this hash, try next
                            continue;
                        } catch (\InvalidArgumentException $ex) {
                            // driver not available, try next
                            continue;
                        }
                    }
                }
            }
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
        try {
            if (Hash::check($password, $user->password)) {
                return true;
            }
        } catch (RuntimeException $e) {
            // Try other drivers if the configured driver can't validate the hash
            $drivers = ['argon2id', 'argon', 'bcrypt'];
            foreach ($drivers as $driver) {
                try {
                    if (Hash::driver($driver)->check($password, $user->password)) {
                        // If matched, re-hash with default driver
                        $user->password = Hash::make($password);
                        $user->save();
                        return true;
                    }
                } catch (RuntimeException $ex) {
                    continue;
                } catch (\InvalidArgumentException $ex) {
                    continue;
                }
            }
        }

        return false;
    }
}
