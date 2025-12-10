@extends('layouts.provider')

@section('title', 'Time Slots - Provider')

@section('provider-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Time Slots</h2>
    <a href="{{ route('provider.time-slots.create') }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add Time Slot
    </a>
</div>

<div class="card">
    <div class="card-body">
        @forelse($timeSlots as $slot)
            <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <h5 class="mb-1">{{ $slot->start_datetime->format('l, F d, Y') }}</h5>
                    <p class="mb-0">
                        <i class="bi bi-clock"></i> 
                        {{ $slot->start_datetime->format('g:i A') }} - {{ $slot->end_datetime->format('g:i A') }}
                    </p>
                </div>
                <div class="text-end">
                    @switch($slot->status)
                        @case('available')
                            <span class="badge bg-success mb-2">Available</span>
                            @break
                        @case('reserved')
                            <span class="badge bg-warning mb-2">Reserved</span>
                            @break
                        @case('blocked')
                            <span class="badge bg-secondary mb-2">Blocked</span>
                            @break
                    @endswitch
                    <br>
                    @if($slot->status !== 'reserved')
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('provider.time-slots.edit', $slot->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('provider.time-slots.destroy', $slot->id) }}" 
                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <small class="text-muted">Has booking</small>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted text-center mb-0">
                No time slots yet. <a href="{{ route('provider.time-slots.create') }}">Add your first time slot</a>
            </p>
        @endforelse
    </div>
</div>

@if($timeSlots->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $timeSlots->links() }}
    </div>
@endif
@endsection
