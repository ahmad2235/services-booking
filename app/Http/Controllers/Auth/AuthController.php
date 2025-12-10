<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Requests\Auth\RegisterProviderRequest;
use App\Services\AuthService;
use App\Repositories\LocationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected LocationRepository $locationRepository
    ) {}

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($this->authService->login($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = $this->authService->user();
            
            // Redirect based on role
            return match($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'provider' => redirect()->intended(route('provider.dashboard')),
                default => redirect()->intended(route('customer.dashboard')),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('email');
    }

    /**
     * Show customer registration form.
     */
    public function showRegisterCustomerForm(): View
    {
        $locations = $this->locationRepository->getGroupedByCity();
        return view('auth.register-customer', compact('locations'));
    }

    /**
     * Handle customer registration.
     */
    public function registerCustomer(RegisterCustomerRequest $request): RedirectResponse
    {
        $userData = $request->only(['name', 'email', 'password', 'phone']);
        $profileData = $request->only(['city', 'area', 'address_details']);

        $user = $this->authService->registerCustomer($userData, $profileData);

        // Auto-login after registration
        $this->authService->login(['email' => $user->email, 'password' => $request->password]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }

    /**
     * Show provider registration form.
     */
    public function showRegisterProviderForm(): View
    {
        $locations = $this->locationRepository->getGroupedByCity();
        return view('auth.register-provider', compact('locations'));
    }

    /**
     * Handle provider registration.
     */
    public function registerProvider(RegisterProviderRequest $request): RedirectResponse
    {
        $userData = $request->only(['name', 'email', 'password', 'phone']);
        $profileData = $request->only([
            'title', 'bio', 'years_of_experience', 
            'min_price', 'max_price', 'coverage_description'
        ]);

        $user = $this->authService->registerProvider($userData, $profileData);

        // Auto-login after registration
        $this->authService->login(['email' => $user->email, 'password' => $request->password]);

        return redirect()->route('provider.dashboard')
            ->with('success', 'Welcome! Your provider account has been created successfully.');
    }

    /**
     * Handle logout.
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
