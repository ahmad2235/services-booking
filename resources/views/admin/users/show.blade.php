@extends('layouts.admin')

@section('title', 'User Details - Admin')

@section('admin-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">{{ $user->name }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">User Details</h4>
                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="mb-1"><strong>Role:</strong> 
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'provider' ? 'bg-primary' : 'bg-info') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Joined:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($user->role === 'customer' && $user->customerProfile)
                            <h5>Customer Profile</h5>
                            <p class="mb-1"><strong>Phone:</strong> {{ $user->customerProfile->phone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Address:</strong> {{ $user->customerProfile->address ?? 'N/A' }}</p>
                        @elseif($user->role === 'provider' && $user->providerProfile)
                            <h5>Provider Profile</h5>
                            <p class="mb-1"><strong>Company:</strong> {{ $user->providerProfile->company_name }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $user->providerProfile->phone ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Experience:</strong> {{ $user->providerProfile->experience_years ?? 0 }} years</p>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($user->providerProfile->avg_rating))
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                                <span>({{ number_format($user->providerProfile->avg_rating, 1) }})</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($user->role === 'provider' && $user->providerProfile && $user->providerProfile->bio)
                    <div class="mb-4">
                        <h5>Bio</h5>
                        <p class="mb-0">{{ $user->providerProfile->bio }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    @if($user->id !== Auth::id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($user->role === 'provider' && $user->providerProfile)
            <!-- Provider Services -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Services ({{ $user->providerProfile->providerServices->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($user->providerProfile->providerServices as $service)
                        <p class="mb-2">
                            {{ $service->service->name }}
                            <span class="badge bg-light text-dark">${{ number_format($service->price, 2) }}</span>
                        </p>
                    @empty
                        <p class="text-muted mb-0">No services.</p>
                    @endforelse
                </div>
            </div>

            <!-- Provider Locations -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Service Areas</h5>
                </div>
                <div class="card-body">
                    @forelse($user->providerProfile->locations as $location)
                        <p class="mb-2">
                            <i class="bi bi-geo-alt"></i> {{ $location->city }}, {{ $location->state }}
                        </p>
                    @empty
                        <p class="text-muted mb-0">No service areas.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
