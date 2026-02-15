@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🚌 Edit Vehicle</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.vehicles.index') }}">Vehicles</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Vehicle Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transport.vehicles.update', $vehicle) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="vechile_no" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('vechile_no') is-invalid @enderror" 
                                   id="vechile_no" 
                                   name="vechile_no" 
                                   value="{{ old('vechile_no', $vehicle->vechile_no) }}" 
                                   placeholder="e.g., TS 09 EA 1234"
                                   required>
                            @error('vechile_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_of_seats" class="form-label">Number of Seats <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('no_of_seats') is-invalid @enderror" 
                                   id="no_of_seats" 
                                   name="no_of_seats" 
                                   value="{{ old('no_of_seats', $vehicle->no_of_seats) }}" 
                                   min="1" 
                                   max="100"
                                   placeholder="e.g., 40"
                                   required>
                            @error('no_of_seats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the total seating capacity of the vehicle</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign Routes (Optional)</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @if($routes->isEmpty())
                                    <p class="text-muted mb-0">No routes available. Please create routes first.</p>
                                @else
                                    @foreach($routes as $route)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="route_id[]" 
                                                   value="{{ $route->route_id }}" 
                                                   id="route_{{ $route->route_id }}"
                                                   {{ in_array($route->route_id, old('route_id', $selectedRoutes)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="route_{{ $route->route_id }}">
                                                {{ $route->route_name }} <span class="text-muted">({{ $route->formatted_cost }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-text">Select the routes this vehicle will serve</div>
                        </div>

                        <div class="mb-3">
                            <label for="driver_id" class="form-label">Assign Driver (Optional)</label>
                            <select class="form-select @error('driver_id') is-invalid @enderror" 
                                    id="driver_id" 
                                    name="driver_id">
                                <option value="">-- Select Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->driver_id }}" 
                                            {{ old('driver_id', $vehicle->driver_id) == $driver->driver_id ? 'selected' : '' }}>
                                        {{ $driver->driver_name }} 
                                        @if($driver->license_number)
                                            ({{ $driver->license_number }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Assign a driver to this vehicle</div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">Vehicle Documentation</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="insurance_number" class="form-label">Insurance Number</label>
                                <input type="text" 
                                       class="form-control @error('insurance_number') is-invalid @enderror" 
                                       id="insurance_number" 
                                       name="insurance_number" 
                                       value="{{ old('insurance_number', $vehicle->insurance_number) }}" 
                                       placeholder="e.g., POL-123456789">
                                @error('insurance_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_expiry" class="form-label">Insurance Expiry Date</label>
                                <input type="date" 
                                       class="form-control @error('insurance_expiry') is-invalid @enderror" 
                                       id="insurance_expiry" 
                                       name="insurance_expiry" 
                                       value="{{ old('insurance_expiry', $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}">
                                @error('insurance_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="permit_number" class="form-label">Permit Number</label>
                                <input type="text" 
                                       class="form-control @error('permit_number') is-invalid @enderror" 
                                       id="permit_number" 
                                       name="permit_number" 
                                       value="{{ old('permit_number', $vehicle->permit_number) }}" 
                                       placeholder="e.g., PER-987654321">
                                @error('permit_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="permit_expiry" class="form-label">Permit Expiry Date</label>
                                <input type="date" 
                                       class="form-control @error('permit_expiry') is-invalid @enderror" 
                                       id="permit_expiry" 
                                       name="permit_expiry" 
                                       value="{{ old('permit_expiry', $vehicle->permit_expiry ? $vehicle->permit_expiry->format('Y-m-d') : '') }}">
                                @error('permit_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Vehicle
                            </button>
                            <a href="{{ route('transport.vehicles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle"></i> Information</h6>
                    <p class="small text-muted mb-2">
                        Update the vehicle details as needed.
                    </p>
                    <p class="small text-muted mb-0">
                        Be careful when changing the number of seats if students are already assigned.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
