@extends('layouts.app')

@section('title', 'Student Transport Assignments')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>👨‍🎓 Student Transport Assignments</h2>
            <p class="text-muted">Manage student transport assignments</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('transport.students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Assign Student
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transport.students.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by reg no or name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="route_id" class="form-select">
                        <option value="">All Routes</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->route_id }}" {{ request('route_id') == $route->route_id ? 'selected' : '' }}>
                                {{ $route->route_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('transport.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-body">
            @if($assignments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">No student assignments found. Click "Assign Student" to add one.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No.</th>
                                <th>Reg. No.</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Monthly Fee</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $index => $assignment)
                            <tr>
                                <td>{{ $assignments->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $assignment->registration_no }}</strong>
                                </td>
                                <td>
                                    @if($assignment->student)
                                        {{ $assignment->student->student_name }}
                                    @else
                                        <span class="text-muted fst-italic">Not found</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->classModel)
                                        <span class="badge bg-secondary">{{ $assignment->classModel->class_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->vehicle)
                                        {{ $assignment->vehicle->vechile_no }}
                                    @else
                                        <span class="text-muted fst-italic">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->route)
                                        {{ $assignment->route->route_name }}
                                    @else
                                        <span class="text-muted fst-italic">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->route)
                                        <span class="badge bg-success">{{ $assignment->route->formatted_cost }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transport.students.edit', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('transport.students.destroy', $assignment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $assignments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
