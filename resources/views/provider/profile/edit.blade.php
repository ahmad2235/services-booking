@extends('layouts.provider')

@section('title', 'Edit Profile - Provider')

@section('provider-content')
<h2 class="mb-4">Edit Profile</h2>

<div class="row">
    <div class="col-lg-8">
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

                <form method="POST" action="{{ route('provider.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               value="{{ old('company_name', $profile->company_name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Business Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone', $profile->phone) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Business Description</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $profile->bio) }}</textarea>
                        <div class="form-text">Describe your business and the services you offer.</div>
                    </div>

                    <div class="mb-3">
                        <label for="experience_years" class="form-label">Years of Experience</label>
                        <input type="number" class="form-control" id="experience_years" name="experience_years" 
                               value="{{ old('experience_years', $profile->experience_years) }}" min="0" max="50">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Stats</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($profile->avg_rating))
                                <i class="bi bi-star-fill fs-4"></i>
                            @else
                                <i class="bi bi-star fs-4"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="mb-0">{{ number_format($profile->avg_rating, 1) }} / 5.0</p>
                </div>
                <hr>
                <p class="mb-1"><strong>Member since:</strong></p>
                <p class="text-muted">{{ Auth::user()->created_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
