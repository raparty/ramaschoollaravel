@extends('layouts.app')

@section('title', 'Edit Warden - School ERP')

@section('content')
<div class="mb-4">
    <h2>👮 Edit Warden</h2>
    <p class="text-muted">Update warden information</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.wardens.update', $warden) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $warden->name) }}" 
                           required
                           maxlength="100"
                           placeholder="Enter warden name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="employee_code" class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('employee_code') is-invalid @enderror" 
                           id="employee_code" 
                           name="employee_code" 
                           value="{{ old('employee_code', $warden->employee_code) }}" 
                           required
                           maxlength="50"
                           placeholder="e.g., EMP001">
                    @error('employee_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $warden->email) }}" 
                           maxlength="100"
                           placeholder="warden@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $warden->phone) }}" 
                           required
                           maxlength="20"
                           placeholder="Enter phone number">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select class="form-select @error('gender') is-invalid @enderror" 
                            id="gender" 
                            name="gender" 
                            required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $warden->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $warden->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $warden->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="date_of_joining" class="form-label">Date of Joining</label>
                    <input type="date" 
                           class="form-control @error('date_of_joining') is-invalid @enderror" 
                           id="date_of_joining" 
                           name="date_of_joining" 
                           value="{{ old('date_of_joining', $warden->date_of_joining) }}">
                    @error('date_of_joining')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="">Select Status</option>
                        <option value="Active" {{ old('status', $warden->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $warden->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="On Leave" {{ old('status', $warden->status) == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" 
                              name="address" 
                              rows="2"
                              maxlength="500"
                              placeholder="Enter complete address">{{ old('address', $warden->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3"
                              placeholder="Enter any additional notes or remarks">{{ old('notes', $warden->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active"
                               value="1"
                               {{ old('is_active', $warden->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Warden
                </button>
                <a href="{{ route('hostel.wardens.show', $warden) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
