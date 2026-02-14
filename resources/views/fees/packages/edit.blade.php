@extends('layouts.app')

@section('title', 'Edit Fee Package - School ERP')

@section('content')
<div class="mb-4">
    <h2>Edit Fee Package</h2>
    <p class="text-muted">Update fee package details</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('fee-packages.update', $feePackage) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="package_name" class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('package_name') is-invalid @enderror" 
                               id="package_name" 
                               name="package_name" 
                               value="{{ old('package_name', $feePackage->package_name) }}" 
                               placeholder="e.g., Class 1 Annual Fee"
                               required>
                        @error('package_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('total_amount') is-invalid @enderror" 
                               id="total_amount" 
                               name="total_amount" 
                               value="{{ old('total_amount', $feePackage->total_amount) }}" 
                               step="0.01"
                               min="0"
                               required>
                        @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $feePackage->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('fee-packages.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Package Information</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Created:</strong> {{ $feePackage->created_at->format('d M Y, h:i A') }}<br>
                    <strong>Last Updated:</strong> {{ $feePackage->updated_at->format('d M Y, h:i A') }}
                </small>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0">⚠️ Note</h6>
            </div>
            <div class="card-body">
                <p class="mb-0 small">
                    Changing the fee package amount will not affect existing student fee records. 
                    Only new admissions will use the updated amount.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
