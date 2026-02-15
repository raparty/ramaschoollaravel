@extends('layouts.app')

@section('title', 'Generate Transfer Certificate')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Generate Transfer Certificate</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-search"></i> Search by Registration Number
                                </h6>
                                <form action="{{ route('students.transfer-certificate.show-by-regno') }}" method="GET" class="mt-3">
                                    <div class="mb-3">
                                        <label for="reg_no" class="form-label">
                                            Student Registration Number <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="reg_no" 
                                            name="reg_no" 
                                            placeholder="Enter Registration Number"
                                            required
                                        >
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-file-earmark-text"></i> Generate Certificate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-people"></i> Search by Student Name
                                </h6>
                                <form action="{{ route('students.transfer-certificate.search') }}" method="GET" class="mt-3">
                                    @if($errors->has('search'))
                                        <div class="alert alert-warning alert-dismissible fade show">
                                            {{ $errors->first('search') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Student Name 
                                            <span class="text-muted" aria-label="Optional field">(Optional)</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="name" 
                                            name="name" 
                                            placeholder="Enter student name to search"
                                            value="{{ request('name') }}"
                                            aria-describedby="nameHelp"
                                        >
                                        <small id="nameHelp" class="form-text text-muted">Provide name and/or class to search</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_id" class="form-label">
                                            Class 
                                            <span class="text-muted" aria-label="Optional field">(Optional)</span>
                                        </label>
                                        <select class="form-select" id="class_id" name="class_id">
                                            <option value="">-- Select Class --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="bi bi-search"></i> Search Students
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($students) && count($students) > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Search Results ({{ $isLimited ?? false ? count($students) . '+' : count($students) }} student(s){{ isset($isLimited) && $isLimited ? ' - showing first ' . count($students) : '' }})</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($isLimited) && $isLimited)
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle"></i> 
                                    Showing first {{ count($students) }} results. More students match your search. 
                                    Please refine your search criteria to see more specific results.
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Reg. No.</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Guardian Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $index => $student)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $student->reg_no }}</strong></td>
                                                <td>{{ $student->student_name }}</td>
                                                <td>{{ $student->class?->name ?? 'N/A' }}</td>
                                                <td>{{ $student->guardian_name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('students.transfer-certificate.show', $student->reg_no) }}" 
                                                       class="btn btn-sm btn-primary"
                                                       target="_blank">
                                                        <i class="bi bi-file-earmark-text"></i> Generate TC
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif(isset($students))
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle"></i> No students found matching your search criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
