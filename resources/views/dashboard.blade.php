@extends('layouts.app')

@section('title', 'Dashboard - School ERP System')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Institutional Dashboard</h2>
        <p class="text-muted">Welcome to the School ERP System</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Students</h6>
                <h3 class="card-title">{{ $stats['total_students'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Books</h6>
                <h3 class="card-title">{{ $stats['total_books'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Books Issued</h6>
                <h3 class="card-title">{{ $stats['books_issued'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Fees Collected</h6>
                <h3 class="card-title">₹{{ number_format($stats['total_fees_collected'], 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Admissions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_admissions as $admission)
                                <tr>
                                    <td>{{ $admission->reg_no }}</td>
                                    <td>{{ $admission->student_name }}</td>
                                    <td>{{ $admission->class?->name ?? 'N/A' }}</td>
                                    <td>{{ $admission->admission_date?->format('d-m-Y') ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent admissions</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Overdue Books</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdue_books as $issue)
                                <tr>
                                    <td>{{ $issue->admission?->student_name ?? 'N/A' }}</td>
                                    <td>{{ $issue->book?->title ?? 'N/A' }}</td>
                                    <td class="text-danger">{{ $issue->due_date?->format('d-m-Y') ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No overdue books</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admissions.create') }}" class="btn btn-outline-primary w-100">
                            👨‍🎓 New Admission
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('fee-packages.index') }}" class="btn btn-outline-primary w-100">
                            💰 Manage Fees
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-primary w-100">
                            📚 Library Management
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-primary w-100">
                            📊 Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
