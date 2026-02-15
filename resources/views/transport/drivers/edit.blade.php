@extends('layouts.app')

@section('title', 'Edit Driver')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>👨‍✈️ Edit Driver</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.drivers.index') }}">Drivers</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Driver Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transport.drivers.update', $driver) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="driver_name" class="form-label">Driver Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('driver_name') is-invalid @enderror" 
                                   id="driver_name" 
                                   name="driver_name" 
                                   value="{{ old('driver_name', $driver->driver_name) }}" 
                                   placeholder="e.g., Rajesh Kumar"
                                   required>
                            @error('driver_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label">License Number</label>
                                <input type="text" 
                                       class="form-control @error('license_number') is-invalid @enderror" 
                                       id="license_number" 
                                       name="license_number" 
                                       value="{{ old('license_number', $driver->license_number) }}" 
                                       placeholder="e.g., DL-1420110012345">
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="aadhar_number" class="form-label">Aadhar Number</label>
                                <input type="text" 
                                       class="form-control @error('aadhar_number') is-invalid @enderror" 
                                       id="aadhar_number" 
                                       name="aadhar_number" 
                                       value="{{ old('aadhar_number', $driver->aadhar_number) }}" 
                                       placeholder="e.g., 1234 5678 9012">
                                @error('aadhar_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" 
                                   class="form-control @error('contact_number') is-invalid @enderror" 
                                   id="contact_number" 
                                   name="contact_number" 
                                   value="{{ old('contact_number', $driver->contact_number) }}" 
                                   placeholder="e.g., +91 9876543210">
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3"
                                      placeholder="Enter driver's address">{{ old('address', $driver->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', $driver->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $driver->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Driver
                            </button>
                            <a href="{{ route('transport.drivers.index') }}" class="btn btn-secondary">
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
                        Update driver details including their license and Aadhar information.
                    </p>
                    @if($driver->vehicles->count() > 0)
                        <hr>
                        <h6 class="card-subtitle mb-2">Assigned Vehicles</h6>
                        <ul class="small mb-0">
                            @foreach($driver->vehicles as $vehicle)
                                <li>{{ $vehicle->vechile_no }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
