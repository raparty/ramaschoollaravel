@extends('layouts.app')

@section('title', 'Salary History - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Salary History</h2>
        <p class="text-muted mb-0">{{ $staff->name }} ({{ $staff->staff_id }})</p>
    </div>
    <div>
        <a href="{{ route('salaries.process') }}?staff_id={{ $staff->id }}" class="btn btn-primary me-2">
            <i class="bi bi-cash-stack"></i> Process Salary
        </a>
        <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('salaries.history', $staff->id) }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            @for($y = now()->year; $y >= now()->year - 10; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Salary History Table -->
        <div class="card">
            <div class="card-body">
                @if($salaries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month/Year</th>
                                    <th>Basic</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaries as $salary)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::create($salary->year, $salary->month)->format('M Y') }}</strong>
                                        </td>
                                        <td>₹{{ number_format($salary->basic_salary, 2) }}</td>
                                        <td>₹{{ number_format($salary->allowances, 2) }}</td>
                                        <td>₹{{ number_format($salary->deductions, 2) }}</td>
                                        <td><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
                                        <td>
                                            @if($salary->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                                @if($salary->payment_date)
                                                    <br><small>{{ $salary->payment_date->format('d M, Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('salaries.slip', $salary->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-printer"></i> Slip
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total</th>
                                    <th>₹{{ number_format($salaries->sum('basic_salary'), 2) }}</th>
                                    <th>₹{{ number_format($salaries->sum('allowances'), 2) }}</th>
                                    <th>₹{{ number_format($salaries->sum('deductions'), 2) }}</th>
                                    <th><strong>₹{{ number_format($salaries->sum('net_salary'), 2) }}</strong></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $salaries->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Salary History</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['year', 'status']))
                                No salary records match your filter criteria.
                            @else
                                No salary has been processed for this staff member yet.
                            @endif
                        </p>
                        <a href="{{ route('salaries.process') }}?staff_id={{ $staff->id }}" class="btn btn-primary">
                            <i class="bi bi-cash-stack"></i> Process First Salary
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Staff Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Staff Information</h5>
            </div>
            <div class="card-body text-center">
                @if($staff->photo)
                    <img src="{{ $staff->photoUrl }}" alt="{{ $staff->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 100px; height: 100px; font-size: 40px;">
                        {{ substr($staff->name, 0, 1) }}
                    </div>
                @endif
                <h5>{{ $staff->name }}</h5>
                <p class="text-muted mb-2">{{ $staff->staff_id }}</p>
                <span class="badge bg-info">{{ $staff->department->name ?? 'N/A' }}</span>
                <br>
                <span class="badge bg-secondary mt-1">{{ $staff->position->title ?? 'N/A' }}</span>
            </div>
        </div>
        
        <!-- Summary Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Summary Statistics</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Records:</span>
                    <strong>{{ $totalRecords }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Paid:</span>
                    <strong class="text-success">₹{{ number_format($totalPaid, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Pending:</span>
                    <strong class="text-warning">₹{{ number_format($totalPending, 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span><strong>Grand Total:</strong></span>
                    <strong class="text-primary">₹{{ number_format($totalPaid + $totalPending, 2) }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('salaries.process') }}?staff_id={{ $staff->id }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-cash-stack"></i> Process Salary
                </a>
                <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-person"></i> View Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
