@extends('layouts.provider')

@section('title', 'Add Time Slot - Provider')

@section('provider-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('provider.time-slots.index') }}">Time Slots</a></li>
        <li class="breadcrumb-item active">Add Time Slot</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Add New Time Slot</h4>
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

                <form method="POST" action="{{ route('provider.time-slots.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="start_datetime" class="form-label">Start Date & Time</label>
                        <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" 
                               value="{{ old('start_datetime') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_datetime" class="form-label">End Date & Time</label>
                        <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" 
                               value="{{ old('end_datetime') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                        <div class="form-text">Blocked slots cannot be booked by customers.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add Time Slot
                        </button>
                        <a href="{{ route('provider.time-slots.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
