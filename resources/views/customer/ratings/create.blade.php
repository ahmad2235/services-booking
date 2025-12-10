@extends('layouts.customer')

@section('title', 'Rate Service - Customer')

@section('customer-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customer.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customer.bookings.show', $booking->id) }}">Booking #{{ $booking->id }}</a></li>
        <li class="breadcrumb-item active">Rate</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Rate Your Experience</h4>
            </div>
            <div class="card-body">
                <!-- Service Info -->
                <div class="text-center mb-4">
                    <h5>{{ $booking->providerService->service->name }}</h5>
                    <p class="text-muted mb-1">{{ $booking->providerService->provider->company_name }}</p>
                    <p class="text-muted small">{{ $booking->scheduled_at->format('M d, Y g:i A') }}</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.ratings.store', $booking->id) }}">
                    @csrf

                    <div class="mb-4 text-center">
                        <label class="form-label d-block">Your Rating</label>
                        <div class="rating-input">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating_value" value="{{ $i }}" 
                                       {{ old('rating_value') == $i ? 'checked' : '' }} required>
                                <label for="star{{ $i }}" title="{{ $i }} stars">
                                    <i class="bi bi-star-fill fs-2"></i>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="form-label">Your Review (Optional)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" 
                                  placeholder="Share your experience with this service...">{{ old('comment') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-star"></i> Submit Rating
                        </button>
                        <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 5px;
    }
    .rating-input input {
        display: none;
    }
    .rating-input label {
        cursor: pointer;
        color: #ddd;
        transition: color 0.2s;
    }
    .rating-input label:hover,
    .rating-input label:hover ~ label,
    .rating-input input:checked ~ label {
        color: #ffc107;
    }
</style>
@endpush
@endsection
