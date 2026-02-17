@extends('layouts.app')

@section('title', 'New Hostel - School ERP')

@section('content')
<div class="mb-4">
    <h2>🏠 New Hostel</h2>
    <p class="text-muted">Add a new hostel building</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Hostel Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required
                           maxlength="100">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">Select Type</option>
                        <option value="Boys" {{ old('type') == 'Boys' ? 'selected' : '' }}>Boys</option>
                        <option value="Girls" {{ old('type') == 'Girls' ? 'selected' : '' }}>Girls</option>
                        <option value="Junior" {{ old('type') == 'Junior' ? 'selected' : '' }}>Junior</option>
                        <option value="Senior" {{ old('type') == 'Senior' ? 'selected' : '' }}>Senior</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="total_capacity" class="form-label">Total Capacity <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('total_capacity') is-invalid @enderror" 
                           id="total_capacity" 
                           name="total_capacity" 
                           value="{{ old('total_capacity') }}" 
                           required
                           min="1">
                    @error('total_capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" 
                           class="form-control @error('address') is-invalid @enderror" 
                           id="address" 
                           name="address" 
                           value="{{ old('address') }}"
                           maxlength="500">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input @error('is_active') is-invalid @enderror" 
                               id="is_active" 
                               name="is_active"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('hostel.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Hostel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
