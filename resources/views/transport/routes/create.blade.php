@extends('layouts.app')

@section('title', 'Add New Route')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🚏 Add New Route</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.routes.index') }}">Routes</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Route Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transport.routes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="route_name" class="form-label">Route Name / Destination <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('route_name') is-invalid @enderror" 
                                   id="route_name" 
                                   name="route_name" 
                                   value="{{ old('route_name') }}" 
                                   placeholder="e.g., City Center, Railway Station, Market Area"
                                   required>
                            @error('route_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cost" class="form-label">Monthly Cost (₹) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('cost') is-invalid @enderror" 
                                   id="cost" 
                                   name="cost" 
                                   value="{{ old('cost') }}" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="e.g., 500.00"
                                   required>
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the monthly transport fee for this route</div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Route
                            </button>
                            <a href="{{ route('transport.routes.index') }}" class="btn btn-secondary">
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
                        Transport routes represent the different destinations that school buses travel to.
                    </p>
                    <p class="small text-muted mb-0">
                        The monthly cost is the fee charged to students using this particular route.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
