@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px;">
        <nav class="nav flex-column py-3">
            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}" href="{{ route('customer.bookings.index') }}">
                <i class="bi bi-calendar-check"></i> My Bookings
            </a>
            <hr class="mx-3">
            <a class="nav-link" href="{{ route('services') }}">
                <i class="bi bi-search"></i> Find Services
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        @yield('customer-content')
    </div>
</div>
@endsection
