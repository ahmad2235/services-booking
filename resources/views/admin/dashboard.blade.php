@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('admin-content')
<h2 class="mb-4">Admin Dashboard</h2>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Users</h6>
                        <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Providers</h6>
                        <h2 class="mb-0">{{ $stats['total_providers'] }}</h2>
                    </div>
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Customers</h6>
                        <h2 class="mb-0">{{ $stats['total_customers'] }}</h2>
                    </div>
                    <i class="bi bi-person fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
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
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['total_categories'] }}</h4>
                <p class="text-muted mb-0">Service Categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['total_services'] }}</h4>
                <p class="text-muted mb-0">Services</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['total_ratings'] }}</h4>
                <p class="text-muted mb-0">Ratings</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Add Category
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Add Service
                    </a>
                    <a href="{{ route('admin.ratings.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-star"></i> Moderate Ratings
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                @forelse($recentActions as $action)
                    <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <p class="mb-1">{{ $action->action_type }}: {{ $action->description }}</p>
                        <small class="text-muted">
                            {{ $action->admin->name }} • {{ $action->created_at->diffForHumans() }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
