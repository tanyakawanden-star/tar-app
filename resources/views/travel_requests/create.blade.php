@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Travel Authorization</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('travel-requests.store') }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                Requester Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->nik }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->department }}" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Travel Details
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="destination_city" class="form-label">Destination City *</label>
                        <input type="text" name="destination_city" id="destination_city"
                               class="form-control" value="{{ old('destination_city') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="destination_country" class="form-label">Destination Country</label>
                        <input type="text" name="destination_country" id="destination_country"
                               class="form-control" value="{{ old('destination_country') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="is_overseas"
                                   name="is_overseas" {{ old('is_overseas') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_overseas">
                                Overseas Travel
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="departure_date" class="form-label">Departure Date *</label>
                        <input type="date" name="departure_date" id="departure_date"
                               class="form-control" value="{{ old('departure_date') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="return_date" class="form-label">Return Date *</label>
                        <input type="date" name="return_date" id="return_date"
                               class="form-control" value="{{ old('return_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="purpose" class="form-label">Purpose</label>
                        <textarea name="purpose" id="purpose" rows="3" class="form-control">{{ old('purpose') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Estimated Costs
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="estimated_transport_cost" class="form-label">Transport</label>
                        <input type="number" step="0.01" name="estimated_transport_cost"
                               id="estimated_transport_cost" class="form-control"
                               value="{{ old('estimated_transport_cost', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="estimated_hotel_cost" class="form-label">Hotel</label>
                        <input type="number" step="0.01" name="estimated_hotel_cost"
                               id="estimated_hotel_cost" class="form-control"
                               value="{{ old('estimated_hotel_cost', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="estimated_meals_cost" class="form-label">Meals</label>
                        <input type="number" step="0.01" name="estimated_meals_cost"
                               id="estimated_meals_cost" class="form-control"
                               value="{{ old('estimated_meals_cost', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="estimated_other_cost" class="form-label">Other</label>
                        <input type="number" step="0.01" name="estimated_other_cost"
                               id="estimated_other_cost" class="form-control"
                               value="{{ old('estimated_other_cost', 0) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('travel-requests.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                Submit Travel Authorization
            </button>
        </div>
    </form>
</div>
@endsection
