@extends('layouts.app')

@section('title', 'Edit Room - School ERP')

@section('content')
<div class="mb-4">
    <h2>🚪 Edit Room</h2>
    <p class="text-muted">Update room details</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.rooms.update', $room) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="floor_id" class="form-label">Floor <span class="text-danger">*</span></label>
                    <select class="form-select @error('floor_id') is-invalid @enderror" 
                            id="floor_id" 
                            name="floor_id" 
                            required>
                        <option value="">Select Floor</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}" {{ old('floor_id', $room->floor_id) == $floor->id ? 'selected' : '' }}>
                                {{ $floor->block->hostel->name }} - {{ $floor->block->name }} - {{ $floor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('floor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('room_number') is-invalid @enderror" 
                           id="room_number" 
                           name="room_number" 
                           value="{{ old('room_number', $room->room_number) }}" 
                           required
                           maxlength="50"
                           placeholder="e.g., 101, A-201">
                    @error('room_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="room_type" class="form-label">Room Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('room_type') is-invalid @enderror" 
                            id="room_type" 
                            name="room_type" 
                            required>
                        <option value="">Select Type</option>
                        <option value="Single" {{ old('room_type', $room->room_type) == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Double" {{ old('room_type', $room->room_type) == 'Double' ? 'selected' : '' }}>Double</option>
                        <option value="Triple" {{ old('room_type', $room->room_type) == 'Triple' ? 'selected' : '' }}>Triple</option>
                        <option value="Dormitory" {{ old('room_type', $room->room_type) == 'Dormitory' ? 'selected' : '' }}>Dormitory</option>
                    </select>
                    @error('room_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="max_strength" class="form-label">Max Strength <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('max_strength') is-invalid @enderror" 
                           id="max_strength" 
                           name="max_strength" 
                           value="{{ old('max_strength', $room->max_strength) }}" 
                           required
                           min="1"
                           max="50">
                    @error('max_strength')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($room->current_occupancy > 0)
                        <small class="text-muted">Current occupancy: {{ $room->current_occupancy }}</small>
                    @endif
                </div>
                <div class="col-md-4">
                    <label for="area_sqft" class="form-label">Area (sq.ft)</label>
                    <input type="number" 
                           class="form-control @error('area_sqft') is-invalid @enderror" 
                           id="area_sqft" 
                           name="area_sqft" 
                           value="{{ old('area_sqft', $room->area_sqft) }}" 
                           min="0"
                           step="0.01"
                           placeholder="e.g., 120.50">
                    @error('area_sqft')
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
                              rows="3"
                              placeholder="Enter room description or notes">{{ old('description', $room->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="has_attached_bathroom" 
                               name="has_attached_bathroom"
                               value="1"
                               {{ old('has_attached_bathroom', $room->has_attached_bathroom) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_attached_bathroom">
                            Has Attached Bathroom
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active"
                               value="1"
                               {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Room
                </button>
                <a href="{{ route('hostel.rooms.show', $room) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
