@extends('layouts.app')

@section('title', 'Home Services Booking')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Find Trusted Home Service Providers</h1>
        <p class="lead mb-4">Book professional services for your home with just a few clicks</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('services') }}" class="btn btn-light btn-lg">Browse Services</a>
            @guest
                <a href="{{ route('register.provider') }}" class="btn btn-outline-light btn-lg">Become a Provider</a>
            @endguest
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Popular Service Categories</h2>
        <div class="row g-4">
            @forelse($categories as $category)
                <div class="col-md-4 col-lg-3">
                    <div class="card card-hover h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-tools fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($category->description, 60) }}</p>
                            <a href="{{ route('services', ['category' => $category->id]) }}" class="btn btn-outline-primary btn-sm">
                                View Services
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No categories available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Providers -->
@if($featuredProviders->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Top Rated Providers</h2>
        <div class="row g-4">
            @foreach($featuredProviders as $provider)
                <div class="col-md-4">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px;">
                                    <span class="text-white fs-4">{{ substr($provider->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">{{ $provider->company_name }}</h5>
                                    <small class="text-muted">{{ $provider->user->name }}</small>
                                </div>
                            </div>
                            <p class="card-text text-muted">{{ Str::limit($provider->bio, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($provider->avg_rating))
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    <span class="text-muted ms-1">({{ number_format($provider->avg_rating, 1) }})</span>
                                </div>
                                <a href="{{ route('providers.show', $provider->user_id) }}" class="btn btn-sm btn-primary">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- How It Works -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-search text-white fs-2"></i>
                </div>
                <h4>1. Find a Service</h4>
                <p class="text-muted">Browse through our categories and find the service you need.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-calendar-check text-white fs-2"></i>
                </div>
                <h4>2. Book an Appointment</h4>
                <p class="text-muted">Choose a convenient time slot and book your appointment.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-hand-thumbs-up text-white fs-2"></i>
                </div>
                <h4>3. Get Service & Rate</h4>
                <p class="text-muted">Receive quality service and share your experience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
@guest
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of satisfied customers and service providers.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register.customer') }}" class="btn btn-light btn-lg">Sign Up as Customer</a>
            <a href="{{ route('register.provider') }}" class="btn btn-outline-light btn-lg">Become a Provider</a>
        </div>
    </div>
</section>
@endguest
@endsection
