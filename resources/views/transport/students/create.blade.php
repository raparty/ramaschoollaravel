@extends('layouts.app')

@section('title', 'Assign Student to Transport')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>👨‍🎓 Assign Student to Transport</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.students.index') }}">Student Assignments</a></li>
                    <li class="breadcrumb-item active">Assign New</li>
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
                    <form action="{{ route('transport.students.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="registration_no" class="form-label">Student <span class="text-danger">*</span></label>
                            <select class="form-select @error('registration_no') is-invalid @enderror" 
                                    id="registration_no" 
                                    name="registration_no" 
                                    required>
                                <option value="">-- Select Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->reg_no }}" {{ old('registration_no') == $student->reg_no ? 'selected' : '' }}>
                                        {{ $student->reg_no }} - {{ $student->student_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('registration_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="route_id" class="form-label">Route / Destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('route_id') is-invalid @enderror" 
                                    id="route_id" 
                                    name="route_id" 
                                    required>
                                <option value="">-- Select Route --</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->route_id }}" {{ old('route_id') == $route->route_id ? 'selected' : '' }}>
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
                                    <option value="{{ $vehicle->vechile_id }}" {{ old('vechile_id') == $vehicle->vechile_id ? 'selected' : '' }}>
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
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="session" class="form-label">Academic Session <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('session') is-invalid @enderror" 
                                   id="session" 
                                   name="session" 
                                   value="{{ old('session', $currentSession) }}" 
                                   placeholder="e.g., 2024"
                                   required>
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the academic year for this assignment</div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Assign Student
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
                        Assign students to transport routes and vehicles for the current academic session.
                    </p>
                    <p class="small text-muted mb-2">
                        Make sure the vehicle has available seats before assigning.
                    </p>
                    <p class="small text-muted mb-0">
                        The monthly transport fee is determined by the selected route.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
