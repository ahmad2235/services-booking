@extends('layouts.app')

@section('title', 'Register as Customer - Home Services Booking')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Register as Customer</h3>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.customer') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <select id="city" name="city" class="form-control" required>
                                    <option value="">Select city</option>
                                    @if(isset($locations) && $locations->count())
                                        @foreach($locations->keys() as $city)
                                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="area" class="form-label">Area</label>
                                    <select id="area" name="area" class="form-control" required>
                                        <option value="">Select area</option>

                                        @php
                                            // Resolve areas safely whether $locations is a Collection or an array
                                            $areas = [];
                                            if (isset($locations) && old('city')) {
                                                if ($locations instanceof \Illuminate\Support\Collection) {
                                                    if ($locations->has(old('city'))) {
                                                        $areas = $locations->get(old('city'));
                                                    }
                                                } elseif (is_array($locations) && array_key_exists(old('city'), $locations)) {
                                                    $areas = $locations[old('city')];
                                                }
                                            }
                                        @endphp

                                        @foreach($areas as $loc)
                                            @php
                                                // $loc may be a model/object, an associative array, or a plain string
                                                if (is_object($loc)) {
                                                    $areaValue = $loc->area ?? (property_exists($loc, 'name') ? $loc->name : (string) $loc);
                                                } elseif (is_array($loc)) {
                                                    $areaValue = $loc['area'] ?? ($loc['name'] ?? reset($loc));
                                                } else {
                                                    $areaValue = (string)$loc;
                                                }
                                            @endphp

                                            <option value="{{ $areaValue }}" {{ old('area') == $areaValue ? 'selected' : '' }}>{{ $areaValue }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address_details" class="form-label">Address</label>
                            <textarea class="form-control" id="address_details" name="address_details" rows="2">{{ old('address_details') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Register</button>
                        </div>
                    </form>

                    @push('scripts')
                        <script>
                            (function(){
                                const locations = @json(isset($locations) ? $locations->map(function($group){ return $group->pluck('area'); })->toArray() : []);
                                const citySelect = document.getElementById('city');
                                const areaSelect = document.getElementById('area');

                                function populateAreas(city) {
                                    areaSelect.innerHTML = '<option value="">Select area</option>';
                                    if (!city || !locations[city]) return;
                                    locations[city].forEach(function(area) {
                                        const opt = document.createElement('option');
                                        opt.value = area;
                                        opt.text = area;
                                        areaSelect.appendChild(opt);
                                    });
                                }

                                if (citySelect) {
                                    citySelect.addEventListener('change', function(){
                                        populateAreas(this.value);
                                    });
                                }

                                // populate on page load if old selection exists
                                if (citySelect && citySelect.value) {
                                    populateAreas(citySelect.value);
                                }
                            })();
                        </script>
                    @endpush

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        Already have an account? <a href="{{ route('login') }}">Login</a><br>
                        Want to provide services? <a href="{{ route('register.provider') }}">Register as Provider</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
