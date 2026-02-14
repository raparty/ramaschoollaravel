@extends('layouts.app')

@section('title', 'Create Fee Package - School ERP')

@section('content')
<div class="mb-4">
    <h2>Create Fee Package</h2>
    <p class="text-muted">Add a new fee package</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('fee-packages.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="package_name" class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('package_name') is-invalid @enderror" 
                               id="package_name" 
                               name="package_name" 
                               value="{{ old('package_name') }}" 
                               placeholder="e.g., Class 1 Annual Fee"
                               required>
                        @error('package_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter a descriptive name for this fee package</small>
                    </div>

                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('total_amount') is-invalid @enderror" 
                               id="total_amount" 
                               name="total_amount" 
                               value="{{ old('total_amount') }}" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               required>
                        @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter the total annual fee amount</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Enter package details (optional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('fee-packages.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Use clear, descriptive names like "Class 1 Fee" or "Nursery Annual Fee"</li>
                    <li class="mb-2">Enter the total annual fee amount that students will pay</li>
                    <li class="mb-2">You can divide this into terms later when collecting fees</li>
                    <li>Packages can be assigned to students during admission</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0">📋 Examples</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Nursery Fee</strong><br>₹15,000/year</p>
                <p class="mb-2"><strong>Class 1-5 Fee</strong><br>₹20,000/year</p>
                <p class="mb-0"><strong>Class 6-10 Fee</strong><br>₹25,000/year</p>
            </div>
        </div>
    </div>
</div>
@endsection
