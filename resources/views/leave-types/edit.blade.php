@extends('layouts.app')

@section('title', 'Edit Leave Type')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2><i class="bi bi-pencil"></i> Edit Leave Type</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('leave-types.update', $leaveType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Leave Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $leaveType->name) }}" required placeholder="e.g., Sick Leave, Casual Leave">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="Brief description of this leave type">{{ old('description', $leaveType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_days" class="form-label">Maximum Days Per Year</label>
                            <input type="number" class="form-control @error('max_days') is-invalid @enderror" 
                                   id="max_days" name="max_days" value="{{ old('max_days', $leaveType->max_days) }}" min="1" max="365" placeholder="Leave empty for unlimited">
                            <small class="form-text text-muted">Leave empty for unlimited days per year</small>
                            @error('max_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Requires Approval <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input @error('requires_approval') is-invalid @enderror" type="radio" 
                                       name="requires_approval" id="requires_approval_yes" value="1" 
                                       {{ old('requires_approval', $leaveType->requires_approval) == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="requires_approval_yes">
                                    Yes - Admin approval required
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('requires_approval') is-invalid @enderror" type="radio" 
                                       name="requires_approval" id="requires_approval_no" value="0" 
                                       {{ old('requires_approval', $leaveType->requires_approval) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_approval_no">
                                    No - Auto-approved
                                </label>
                            </div>
                            @error('requires_approval')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" type="radio" 
                                       name="is_active" id="is_active_yes" value="1" 
                                       {{ old('is_active', $leaveType->is_active) == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="is_active_yes">
                                    Active - Staff can apply
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" type="radio" 
                                       name="is_active" id="is_active_no" value="0" 
                                       {{ old('is_active', $leaveType->is_active) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_no">
                                    Inactive - Not available for application
                                </label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('leave-types.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Leave Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
