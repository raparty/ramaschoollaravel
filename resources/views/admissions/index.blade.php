@extends('layouts.app')

@section('title', 'Student Admissions - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Student Admissions</h2>
        <p class="text-muted mb-0">Manage student admission records</p>
    </div>
    <a href="{{ route('admissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Admission
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admissions.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Student name or reg no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Session</label>
                <select name="session" class="form-select">
                    <option value="">All Sessions</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ request('session') == $year ? 'selected' : '' }}>
                            {{ $year }}-{{ $year + 1 }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        @if($admissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Guardian</th>
                            <th>Phone</th>
                            <th>Admission Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admissions as $admission)
                            <tr>
                                <td><strong>{{ $admission->reg_no }}</strong></td>
                                <td>
                                    @if($admission->student_pic)
                                        <img src="{{ asset('storage/students/photos/' . $admission->student_pic) }}" 
                                             alt="{{ $admission->student_name }}" 
                                             class="rounded-circle me-2"
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                    @endif
                                    {{ $admission->student_name }}
                                </td>
                                <td>{{ $admission->class?->name ?? 'N/A' }}</td>
                                <td>{{ $admission->guardian_name ?? '-' }}</td>
                                <td>{{ $admission->guardian_phone ?? '-' }}</td>
                                <td>{{ $admission->admission_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admissions.show', $admission) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            👁️
                                        </a>
                                        <a href="{{ route('admissions.edit', $admission) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            ✏️
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admissions.destroy', $admission) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this admission?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $admissions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">No student admissions found.</p>
                <a href="{{ route('admissions.create') }}" class="btn btn-primary">
                    Add First Admission
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
