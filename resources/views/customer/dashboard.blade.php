@extends('layouts.customer')

@section('title', 'Dashboard - Customer')

@section('customer-content')
<h2 class="mb-4">Welcome, {{ Auth::user()->name }}!</h2>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
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
    <div class="col-md-4">
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
    <div class="col-md-4">
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

<!-- Recent Bookings -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Bookings</h5>
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body">
        @forelse($recentBookings as $booking)
            <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h6 class="mb-1">{{ $booking->providerService->service->name }}</h6>
                    <p class="text-muted mb-0 small">
                        {{ $booking->providerService->provider->company_name }} •
                        {{ $booking->scheduled_at->format('M d, Y g:i A') }}
                    </p>
                </div>
                <div class="text-end">
                    @switch($booking->status)
                        @case('pending')
                            <span class="badge bg-warning">Pending</span>
                            @break
                        @case('confirmed')
                            <span class="badge bg-primary">Confirmed</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success">Completed</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-secondary">Cancelled</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @break
                    @endswitch
                    <br>
                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                        View
                    </a>
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">No bookings yet. <a href="{{ route('services') }}">Browse services</a></p>
        @endforelse
    </div>
</div>
@endsection
