@extends('layouts.app')

@section('title', 'Services - Home Services Booking')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Browse Services</h1>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('services') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select" id="service" name="service_id">
                            <option value="">All Services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option value="">Any City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="area" class="form-label">Area</label>
                        <select class="form-select" id="area" name="area">
                            <option value="">Any Area</option>
                            @if(!empty($areas))
                                @foreach($areas as $area)
                                    <option value="{{ $area->area }}" {{ request('area') == $area->area ? 'selected' : '' }}>{{ $area->area }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="search" class="form-label">Keyword</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search providers or services...">
                    </div>

                    <div class="col-md-2">
                        <label for="min_price" class="form-label">Min Price</label>
                        <input type="number" step="0.01" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" placeholder="0.00">
                    </div>

                    <div class="col-md-2">
                        <label for="max_price" class="form-label">Max Price</label>
                        <input type="number" step="0.01" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" placeholder="0.00">
                    </div>

                    <div class="col-md-2">
                        <label for="min_rating" class="form-label">Min Rating</label>
                        <select class="form-select" id="min_rating" name="min_rating">
                            <option value="">Any</option>
                            @for($r = 1; $r <= 5; $r++)
                                <option value="{{ $r }}" {{ request('min_rating') == $r ? 'selected' : '' }}>{{ $r }}+</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Sort</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="">Best match</option>
                            <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                            <option value="reviews" {{ request('sort_by') == 'reviews' ? 'selected' : '' }}>Most reviews</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="row g-4">
        @forelse($providers as $provider)
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <span class="text-white fs-5">{{ substr($provider->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $provider->company_name }}</h5>
                                <small class="text-muted">{{ $provider->user->name }}</small>
                            </div>
                        </div>

                        <p class="card-text text-muted small">{{ Str::limit($provider->bio, 80) }}</p>

                        @if($provider->years_of_experience)
                            <p class="mb-2"><i class="bi bi-award"></i> {{ $provider->years_of_experience }} years experience</p>
                        @endif

                        <!-- Services Preview -->
                        @if($provider->providerServices->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted">Services:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach($provider->providerServices->take(3) as $providerService)
                                        <span class="badge bg-light text-dark">{{ $providerService->service->name }}</span>
                                    @endforeach
                                    @if($provider->providerServices->count() > 3)
                                        <span class="badge bg-secondary">+{{ $provider->providerServices->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Locations Preview -->
                        @if($provider->locations->count() > 0)
                            <p class="mb-2 small">
                                <i class="bi bi-geo-alt"></i>
                                {{ $provider->locations->pluck('city')->take(2)->implode(', ') }}
                                @if($provider->locations->count() > 2)
                                    +{{ $provider->locations->count() - 2 }} more
                                @endif
                            </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
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
                            <a href="{{ route('providers.show', $provider->id) }}" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No providers found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($providers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $providers->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
