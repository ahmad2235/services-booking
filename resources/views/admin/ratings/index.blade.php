@extends('layouts.admin')

@section('title', 'Ratings - Admin')

@section('admin-content')
<h2 class="mb-4">Ratings Moderation</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Provider</th>
                        <th>Service</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ratings as $rating)
                        <tr class="{{ !$rating->is_visible ? 'table-secondary' : '' }}">
                            <td>{{ $rating->id }}</td>
                            <td>{{ $rating->booking->customer->name }}</td>
                            <td>{{ $rating->booking->providerService->provider->company_name }}</td>
                            <td>{{ $rating->booking->providerService->service->name }}</td>
                            <td>
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->rating_value)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>{{ Str::limit($rating->comment, 30) }}</td>
                            <td>{{ $rating->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ !$rating->is_visible ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $rating->is_visible ? 'Visible' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                @if($rating->is_visible)
                                    <form method="POST" action="{{ route('admin.ratings.hide', $rating->id) }}" 
                                          class="d-inline" onsubmit="return confirm('Are you sure you want to hide this rating?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-eye-slash"></i> Hide
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">Hidden</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No ratings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($ratings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $ratings->links() }}
    </div>
@endif
@endsection
