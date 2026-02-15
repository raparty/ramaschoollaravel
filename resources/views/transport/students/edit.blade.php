@extends('layouts.app')

@section('title', 'Edit Student Transport Assignment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>👨‍🎓 Edit Student Transport Assignment</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.students.index') }}">Student Assignments</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assignment Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transport.students.update', $student) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $student->registration_no }} - {{ $student->student ? $student->student->student_name : 'Unknown' }}" 
                                   disabled>
                            <div class="form-text">Student cannot be changed. Delete and create new assignment if needed.</div>
                        </div>

                        <div class="mb-3">
                            <label for="route_id" class="form-label">Route / Destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('route_id') is-invalid @enderror" 
                                    id="route_id" 
                                    name="route_id" 
                                    required>
                                <option value="">-- Select Route --</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->route_id }}" 
                                            {{ old('route_id', $student->route_id) == $route->route_id ? 'selected' : '' }}>
                                        {{ $route->route_name }} ({{ $route->formatted_cost }})
                                    </option>
                                @endforeach
                            </select>
                            @error('route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vechile_id" class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select class="form-select @error('vechile_id') is-invalid @enderror" 
                                    id="vechile_id" 
                                    name="vechile_id" 
                                    required>
                                <option value="">-- Select Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->vechile_id }}" 
                                            {{ old('vechile_id', $student->vechile_id) == $vehicle->vechile_id ? 'selected' : '' }}>
                                        {{ $vehicle->vechile_no }} ({{ $vehicle->available_seats }} seats available)
                                    </option>
                                @endforeach
                            </select>
                            @error('vechile_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="class_id" class="form-label">Class (Optional)</label>
                            <select class="form-select @error('class_id') is-invalid @enderror" 
                                    id="class_id" 
                                    name="class_id">
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                            {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Academic Session</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $student->session }}" 
                                   disabled>
                            <div class="form-text">Session cannot be changed</div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Assignment
                            </button>
                            <a href="{{ route('transport.students.index') }}" class="btn btn-secondary">
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
                        Update the route or vehicle assignment for this student.
                    </p>
                    <p class="small text-muted mb-0">
                        The monthly transport fee will be updated based on the new route selection.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
