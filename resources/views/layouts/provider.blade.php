@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px;">
        <nav class="nav flex-column py-3">
            <a class="nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}" href="{{ route('provider.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('provider.bookings.*') ? 'active' : '' }}" href="{{ route('provider.bookings.index') }}">
                <i class="bi bi-calendar-check"></i> Bookings
            </a>
            <a class="nav-link {{ request()->routeIs('provider.services.*') ? 'active' : '' }}" href="{{ route('provider.services.index') }}">
                <i class="bi bi-tools"></i> My Services
            </a>
            <a class="nav-link {{ request()->routeIs('provider.time-slots.*') ? 'active' : '' }}" href="{{ route('provider.time-slots.index') }}">
                <i class="bi bi-clock"></i> Time Slots
            </a>
            <hr class="mx-3">
            <a class="nav-link {{ request()->routeIs('provider.profile.*') ? 'active' : '' }}" href="{{ route('provider.profile.edit') }}">
                <i class="bi bi-person-gear"></i> Profile
            </a>
            <a class="nav-link {{ request()->routeIs('provider.locations.*') ? 'active' : '' }}" href="{{ route('provider.locations.edit') }}">
                <i class="bi bi-geo-alt"></i> Locations
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        @yield('provider-content')
    </div>
</div>
@endsection
