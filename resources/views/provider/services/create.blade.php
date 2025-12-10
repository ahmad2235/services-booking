@extends('layouts.provider')

@section('title', 'Add Service - Provider')

@section('provider-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('provider.services.index') }}">Services</a></li>
        <li class="breadcrumb-item active">Add Service</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Add New Service</h4>
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

                <form method="POST" action="{{ route('provider.services.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="service_id" class="form-label">Select Service</label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">Choose a service...</option>
                            @foreach($services as $category => $categoryServices)
                                <optgroup label="{{ $category }}">
                                    @foreach($categoryServices as $service)
                                        <option value="{{ $service->id }}" 
                                                data-price="{{ $service->base_price }}"
                                                data-duration="{{ $service->duration_minutes }}"
                                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - ${{ number_format($service->base_price, 2) }} ({{ $service->duration_minutes }} min)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Your Price ($)</label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="{{ old('price') }}" step="0.01" min="0" required>
                        <div class="form-text">Set your own price for this service.</div>
                    </div>

                    <div class="mb-3">
                        <label for="custom_description" class="form-label">Custom Description (Optional)</label>
                        <textarea class="form-control" id="custom_description" name="custom_description" rows="3" 
                                  placeholder="Add any additional details about how you provide this service...">{{ old('custom_description') }}</textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible to customers)</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add Service
                        </button>
                        <a href="{{ route('provider.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('service_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const price = selected.dataset.price;
        if (price) {
            document.getElementById('price').value = price;
        }
    });
</script>
@endpush
@endsection
