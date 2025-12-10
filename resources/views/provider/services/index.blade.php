@extends('layouts.provider')

@section('title', 'My Services - Provider')

@section('provider-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Services</h2>
    <a href="{{ route('provider.services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add Service
    </a>
</div>

<div class="card">
    <div class="card-body">
        @forelse($services as $providerService)
            <div class="d-flex justify-content-between align-items-start py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h5 class="mb-1">{{ $providerService->service->name }}</h5>
                    <p class="text-muted mb-1">{{ $providerService->service->category->name }}</p>
                    @if($providerService->custom_description)
                        <p class="small mb-1">{{ $providerService->custom_description }}</p>
                    @endif
                    <p class="mb-0 small">
                        <i class="bi bi-clock"></i> {{ $providerService->service->duration_minutes }} min
                    </p>
                </div>
                <div class="text-end">
                    <p class="h5 text-primary mb-2">${{ number_format($providerService->price, 2) }}</p>
                    <span class="badge {{ $providerService->is_active ? 'bg-success' : 'bg-secondary' }} mb-2">
                        {{ $providerService->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <br>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('provider.services.edit', $providerService->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('provider.services.destroy', $providerService->id) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">
                No services yet. <a href="{{ route('provider.services.create') }}">Add your first service</a>
            </p>
        @endforelse
    </div>
</div>

@if($services->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
    </div>
@endif
@endsection
