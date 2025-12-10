@extends('layouts.provider')

@section('title', 'Dashboard - Provider')

@section('provider-content')
<h2 class="mb-4">Welcome, {{ Auth::user()->name }}!</h2>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Bookings</h6>
                        <h2 class="mb-0">{{ $stats['total_bookings'] }}</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pending</h6>
                        <h2 class="mb-0">{{ $stats['pending_bookings'] }}</h2>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Confirmed</h6>
                        <h2 class="mb-0">{{ $stats['confirmed_bookings'] }}</h2>
                    </div>
                    <i class="bi bi-check2-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Completed</h6>
                        <h2 class="mb-0">{{ $stats['completed_bookings'] }}</h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="rating-stars mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['avg_rating']))
                            <i class="bi bi-star-fill fs-4"></i>
                        @else
                            <i class="bi bi-star fs-4"></i>
                        @endif
                    @endfor
                </div>
                <h4 class="mb-0">{{ number_format($stats['avg_rating'], 1) }}</h4>
                <p class="text-muted mb-0">Average Rating</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['total_services'] }}</h4>
                <p class="text-muted mb-0">Services Offered</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">${{ number_format($stats['total_earnings'], 2) }}</h4>
                <p class="text-muted mb-0">Total Earnings</p>
            </div>
        </div>
    </div>
</div>

<!-- Pending Bookings -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Pending Bookings</h5>
        <a href="{{ route('provider.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body">
        @forelse($pendingBookings as $booking)
            <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h6 class="mb-1">{{ $booking->providerService->service->name }}</h6>
                    <p class="text-muted mb-0 small">
                        {{ $booking->customer->name }} •
                        {{ $booking->scheduled_at->format('M d, Y g:i A') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('provider.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                        Review
                    </a>
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">No pending bookings.</p>
        @endforelse
    </div>
</div>

<!-- Upcoming Confirmed Bookings -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Upcoming Confirmed Bookings</h5>
        <a href="{{ route('provider.bookings.index', ['status' => 'confirmed']) }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body">
        @forelse($upcomingBookings as $booking)
            <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h6 class="mb-1">{{ $booking->providerService->service->name }}</h6>
                    <p class="text-muted mb-0 small">
                        {{ $booking->customer->name }} •
                        {{ $booking->scheduled_at->format('M d, Y g:i A') }}
                    </p>
                </div>
                <div>
                    <span class="badge bg-primary me-2">Confirmed</span>
                    <a href="{{ route('provider.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                        View
                    </a>
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">No upcoming bookings.</p>
        @endforelse
    </div>
</div>
@endsection
