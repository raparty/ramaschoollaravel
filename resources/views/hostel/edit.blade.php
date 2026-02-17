@extends('layouts.app')

@section('title', 'Edit Hostel - School ERP')

@section('content')
<div class="mb-4">
    <h2>🏠 Edit Hostel</h2>
    <p class="text-muted">Update hostel information</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.update', $hostel) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Hostel Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $hostel->name) }}" 
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
                        <option value="Boys" {{ old('type', $hostel->type) == 'Boys' ? 'selected' : '' }}>Boys</option>
                        <option value="Girls" {{ old('type', $hostel->type) == 'Girls' ? 'selected' : '' }}>Girls</option>
                        <option value="Junior" {{ old('type', $hostel->type) == 'Junior' ? 'selected' : '' }}>Junior</option>
                        <option value="Senior" {{ old('type', $hostel->type) == 'Senior' ? 'selected' : '' }}>Senior</option>
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
                           value="{{ old('total_capacity', $hostel->total_capacity) }}" 
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
                           value="{{ old('address', $hostel->address) }}"
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
                              rows="4">{{ old('description', $hostel->description) }}</textarea>
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
                               {{ old('is_active', $hostel->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('hostel.show', $hostel) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Hostel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
