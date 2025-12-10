@extends('layouts.provider')

@section('title', 'Service Areas - Provider')

@section('provider-content')
<h2 class="mb-4">Service Areas</h2>

<div class="card">
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

        <form method="POST" action="{{ route('provider.locations.update') }}">
            @csrf
            @method('PUT')

            <p class="text-muted mb-4">Select the locations where you provide services.</p>

            <div class="row g-3">
                @foreach($locations as $location)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="location_ids[]" 
                                   value="{{ $location->id }}" id="location{{ $location->id }}"
                                   {{ in_array($location->id, $selectedLocationIds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="location{{ $location->id }}">
                                {{ $location->city }}, {{ $location->state }}
                                @if($location->zip_code)
                                    <small class="text-muted">({{ $location->zip_code }})</small>
                                @endif
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($locations->isEmpty())
                <div class="alert alert-info">
                    No locations available. Please contact the administrator.
                </div>
            @endif

            <hr class="my-4">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Service Areas
            </button>
        </form>
    </div>
</div>
@endsection
