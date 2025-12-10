@extends('layouts.provider')

@section('title', 'Edit Service - Provider')

@section('provider-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('provider.services.index') }}">Services</a></li>
        <li class="breadcrumb-item active">Edit Service</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Service</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('provider.services.update', $providerService->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <input type="text" class="form-control" value="{{ $providerService->service->name }} ({{ $providerService->service->category->name }})" disabled>
                        <div class="form-text">Service type cannot be changed. Delete and create a new one if needed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Your Price ($)</label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="{{ old('price', $providerService->price) }}" step="0.01" min="0" required>
                        <div class="form-text">Base price: ${{ number_format($providerService->service->base_price, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="custom_description" class="form-label">Custom Description (Optional)</label>
                        <textarea class="form-control" id="custom_description" name="custom_description" rows="3">{{ old('custom_description', $providerService->custom_description) }}</textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $providerService->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible to customers)</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <a href="{{ route('provider.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
