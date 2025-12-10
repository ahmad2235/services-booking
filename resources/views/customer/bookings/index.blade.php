@extends('layouts.customer')

@section('title', 'My Bookings - Customer')

@section('customer-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Bookings</h2>
    <a href="{{ route('services') }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> New Booking
    </a>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('customer.bookings.index') }}">All</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" 
           href="{{ route('customer.bookings.index', ['status' => 'pending']) }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'confirmed' ? 'active' : '' }}" 
           href="{{ route('customer.bookings.index', ['status' => 'confirmed']) }}">Confirmed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') === 'completed' ? 'active' : '' }}" 
           href="{{ route('customer.bookings.index', ['status' => 'completed']) }}">Completed</a>
    </li>
</ul>

<!-- Bookings List -->
<div class="card">
    <div class="card-body">
        @forelse($bookings as $booking)
            <div class="d-flex justify-content-between align-items-start py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h5 class="mb-1">{{ $booking->providerService->service->name }}</h5>
                    <p class="mb-1">
                        <i class="bi bi-building"></i> {{ $booking->providerService->provider->company_name }}
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-calendar"></i> {{ $booking->scheduled_at->format('M d, Y') }}
                        <i class="bi bi-clock ms-2"></i> {{ $booking->scheduled_at->format('g:i A') }}
                    </p>
                    <p class="text-muted mb-0 small">
                        Booked on {{ $booking->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="text-end">
                    @switch($booking->status)
                        @case('pending')
                            <span class="badge bg-warning mb-2">Pending</span>
                            @break
                        @case('confirmed')
                            <span class="badge bg-primary mb-2">Confirmed</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success mb-2">Completed</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-secondary mb-2">Cancelled</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger mb-2">Rejected</span>
                            @break
                    @endswitch
                    <br>
                    <p class="h5 text-primary mb-2">${{ number_format($booking->total_price, 2) }}</p>
                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">No bookings found.</p>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($bookings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->withQueryString()->links() }}
    </div>
@endif
@endsection
