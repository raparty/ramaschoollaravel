@extends('layouts.app')

@section('title', 'Student Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Student Details</h2>
        <p class="text-muted mb-0">Registration No: <strong>{{ $admission->reg_no }}</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ route('transfer-certificate.show', $admission->reg_no) }}" class="btn btn-info">
            <i class="bi bi-file-earmark-text"></i> Transfer Certificate
        </a>
        <a href="{{ route('admissions.edit', $admission) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form method="POST" 
              action="{{ route('admissions.destroy', $admission) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this admission?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Personal Information Card -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="180">Student Name:</th>
                                    <td><strong>{{ $admission->student_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td>{{ $admission->dob->format('d M Y') }} ({{ $admission->dob->age }} years)</td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td>{{ $admission->gender ?? 'Not Specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Blood Group:</th>
                                    <td>{{ $admission->blood_group ?? 'Not Specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Aadhaar Number:</th>
                                    <td>
                                        {{ $admission->aadhaar_no ?? 'Not Provided' }}
                                        @if($admission->aadhaar_doc_path)
                                            <a href="{{ asset('storage/students/aadhaar/' . $admission->aadhaar_doc_path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary ms-2">
                                                View Document
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($admission->student_pic)
                            <img src="{{ asset('storage/students/photos/' . $admission->student_pic) }}" 
                                 alt="{{ $admission->student_name }}" 
                                 class="img-fluid rounded border"
                                 style="max-width: 200px;">
                        @else
                            <div class="border rounded p-4 bg-light">
                                <p class="text-muted mb-0">No Photo</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Academic Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Class:</th>
                            <td><strong>{{ $admission->class->name ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Admission Date:</th>
                            <td>{{ $admission->admission_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Previous School:</th>
                            <td>{{ $admission->past_school_info ?? 'Not Provided' }}</td>
                        </tr>
                        <tr>
                            <th>Academic Year:</th>
                            <td>{{ $admission->admission_date->format('Y') }}-{{ $admission->admission_date->format('Y') + 1 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Guardian Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Guardian Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Guardian Name:</th>
                            <td><strong>{{ $admission->guardian_name ?? 'Not Provided' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Contact Phone:</th>
                            <td>
                                {{ $admission->guardian_phone ?? 'Not Provided' }}
                                @if($admission->guardian_phone)
                                    <a href="tel:{{ $admission->guardian_phone }}" class="btn btn-sm btn-outline-primary ms-2">
                                        📞 Call
                                    </a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admissions.edit', $admission) }}" class="btn btn-outline-primary">
                        ✏️ Edit Details
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        💰 Collect Fees
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        📚 Issue Book
                    </a>
                    <a href="#" class="btn btn-outline-warning">
                        📝 Attendance
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        📄 Generate TC
                    </a>
                </div>
            </div>
        </div>

        <!-- Fee Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h6 class="mb-0">Fee Summary</h6>
            </div>
            <div class="card-body">
                @if($admission->fees->count() > 0)
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td>Total Fees:</td>
                                <td class="text-end"><strong>₹{{ number_format($admission->fees->sum('amount'), 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td>Paid:</td>
                                <td class="text-end text-success">₹{{ number_format($admission->fees->where('status', 'paid')->sum('amount'), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Pending:</td>
                                <td class="text-end text-danger">₹{{ number_format($admission->fees->where('status', 'pending')->sum('amount'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No fee records found</p>
                @endif
            </div>
        </div>

        <!-- Library Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-info">
                <h6 class="mb-0">Library Books</h6>
            </div>
            <div class="card-body">
                @if($admission->libraryBooks->count() > 0)
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td>Total Issued:</td>
                                <td class="text-end"><strong>{{ $admission->libraryBooks->count() }}</strong></td>
                            </tr>
                            <tr>
                                <td>Unreturned:</td>
                                <td class="text-end text-warning">{{ $admission->libraryBooks->whereNull('return_date')->count() }}</td>
                            </tr>
                            <tr>
                                <td>Returned:</td>
                                <td class="text-end text-success">{{ $admission->libraryBooks->whereNotNull('return_date')->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No books issued</p>
                @endif
            </div>
        </div>

        <!-- Record Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Record Information</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Created:</strong> {{ $admission->created_at ? $admission->created_at->format('d M Y, h:i A') : 'N/A' }}<br>
                    
                </small>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admissions.index') }}" class="btn btn-secondary">
        ← Back to List
    </a>
</div>
@endsection
