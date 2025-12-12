@extends('layouts.app')

@section('title', $provider->company_name . ' - Home Services Booking')

@section('content')
<div class="container py-5">
    <!-- Provider Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-4" 
                             style="width: 80px; height: 80px;">
                            <span class="text-white fs-2">{{ substr($provider->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h1 class="mb-1">{{ $provider->company_name }}</h1>
                            <p class="text-muted mb-2">{{ $provider->user->name }}</p>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($provider->avg_rating))
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                                <span class="ms-2">{{ number_format($provider->avg_rating, 1) }} / 5.0</span>
                                <span class="text-muted">({{ $ratings->total() }} reviews)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($provider->years_of_experience)
                        <p class="mb-1"><i class="bi bi-award"></i> {{ $provider->years_of_experience }} years experience</p>
                    @endif
                    @if($provider->phone)
                        <p class="mb-0"><i class="bi bi-telephone"></i> {{ $provider->phone }}</p>
                    @endif
                </div>
            </div>
            @if($provider->bio)
                <hr>
                <p class="mb-0">{{ $provider->bio }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Services -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-tools"></i> Services Offered</h4>
                </div>
                <div class="card-body">
                    @forelse($provider->providerServices as $providerService)
                        <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <h5 class="mb-1">{{ $providerService->service->name }}</h5>
                                <p class="text-muted mb-1 small">{{ $providerService->service->category->name }}</p>
                                @if($providerService->custom_description)
                                    <p class="mb-0 small">{{ $providerService->custom_description }}</p>
                                @endif
                            </div>
                            <div class="text-end">
                                <p class="h5 text-primary mb-1">${{ number_format($providerService->price, 2) }}</p>
                                <p class="text-muted mb-2 small">{{ $providerService->service->duration_minutes }} min</p>
                                @auth
                                    @if(Auth::user()->role === 'customer')
                                        <a href="{{ route('customer.bookings.create', $providerService->id) }}" 
                                           class="btn btn-primary btn-sm">Book Now</a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login to Book</a>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No services available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Reviews -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-star"></i> Customer Reviews</h4>
                </div>
                <div class="card-body">
                    @forelse($ratings as $rating)
                        <div class="py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $rating->booking->customer->name }}</strong>
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating_value)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                            </div>
                            @if($rating->comment)
                                <p class="mb-1">{{ $rating->comment }}</p>
                            @endif
                            <small class="text-muted">Service: {{ $rating->booking->providerService->service->name }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No reviews yet.</p>
                    @endforelse

                    @if($ratings->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $ratings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Service Areas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Service Areas</h5>
                </div>
                <div class="card-body">
                    @forelse($provider->locations as $location)
                        <p class="mb-2">
                            <i class="bi bi-pin-map"></i>
                            {{ $location->city }}, {{ $location->state }}
                            @if($location->zip_code)
                                {{ $location->zip_code }}
                            @endif
                        </p>
                    @empty
                        <p class="text-muted mb-0">No service areas specified.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Provider Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h3 class="text-primary mb-0">{{ $provider->providerServices->count() }}</h3>
                            <small class="text-muted">Services</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-primary mb-0">{{ $ratings->total() }}</h3>
                            <small class="text-muted">Reviews</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
