@extends('layouts.provider')

@section('title', 'Booking Details - Provider')

@section('provider-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('provider.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item active">Booking #{{ $booking->id }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Booking Details</h4>
                @switch($booking->status)
                    @case('pending')
                        <span class="badge bg-warning fs-6">Pending</span>
                        @break
                    @case('confirmed')
                        <span class="badge bg-primary fs-6">Confirmed</span>
                        @break
                    @case('completed')
                        <span class="badge bg-success fs-6">Completed</span>
                        @break
                    @case('cancelled')
                        <span class="badge bg-secondary fs-6">Cancelled</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger fs-6">Rejected</span>
                        @break
                @endswitch
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Service</h5>
                        <p class="mb-1"><strong>{{ $booking->providerService->service->name }}</strong></p>
                        <p class="text-muted mb-0">{{ $booking->providerService->service->category->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Customer</h5>
                        <p class="mb-1"><strong>{{ $booking->customer->name }}</strong></p>
                        <p class="text-muted mb-0">{{ $booking->customer->email }}</p>
                        @if(optional($booking->customer->customerProfile)->phone)
                            <p class="text-muted mb-0"><i class="bi bi-telephone"></i> {{ $booking->customer->customerProfile->phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Scheduled Date & Time</h5>
                        <p class="mb-1"><i class="bi bi-calendar"></i> {{ $booking->scheduled_at->format('l, F d, Y') }}</p>
                        <p class="mb-0"><i class="bi bi-clock"></i> {{ $booking->scheduled_at->format('g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Duration</h5>
                        <p class="mb-0">{{ $booking->providerService->service->duration_minutes }} minutes</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Service Address</h5>
                    <p class="mb-0"><i class="bi bi-geo-alt"></i> {{ $booking->address }}</p>
                </div>

                @if($booking->notes)
                    <div class="mb-4">
                        <h5>Customer Notes</h5>
                        <p class="mb-0">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    @if($booking->status === 'pending')
                        <form method="POST" action="{{ route('provider.bookings.accept', $booking->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Accept Booking
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Reject Booking
                        </button>
                    @endif

                    @if($booking->status === 'confirmed')
                        <form method="POST" action="{{ route('provider.bookings.complete', $booking->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-all"></i> Mark as Completed
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('provider.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Payment Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Service Price:</span>
                    <span>${{ number_format($booking->total_price, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary h4">${{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Rating (if completed and rated) -->
        @if($booking->rating)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Customer Rating</h5>
                </div>
                <div class="card-body">
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $booking->rating->rating_value)
                                <i class="bi bi-star-fill fs-4"></i>
                            @else
                                <i class="bi bi-star fs-4"></i>
                            @endif
                        @endfor
                    </div>
                    @if($booking->rating->comment)
                        <p class="mb-0">{{ $booking->rating->comment }}</p>
                    @endif
                    <small class="text-muted">Rated on {{ $booking->rating->created_at->format('M d, Y') }}</small>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
@if($booking->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('provider.bookings.reject', $booking->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this booking?</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for rejection (optional)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
