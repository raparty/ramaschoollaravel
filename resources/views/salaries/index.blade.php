@extends('layouts.app')

@section('title', 'Salary Management - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Salary Management</h2>
        <p class="text-muted mb-0">Manage staff salaries and payments</p>
    </div>
    <a href="{{ route('salaries.process') }}" class="btn btn-primary">
        <i class="bi bi-cash-stack"></i> Process Salary
    </a>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="mb-0">₹{{ number_format($totals['basic'], 2) }}</h4>
                <p class="mb-0">Total Basic</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h4 class="mb-0">₹{{ number_format($totals['allowances'], 2) }}</h4>
                <p class="mb-0">Total Allowances</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h4 class="mb-0">₹{{ number_format($totals['deductions'], 2) }}</h4>
                <p class="mb-0">Total Deductions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h4 class="mb-0">₹{{ number_format($totals['net'], 2) }}</h4>
                <p class="mb-0">Total Net</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('salaries.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Salaries Table -->
<div class="card">
    <div class="card-body">
        @if($salaries->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Department</th>
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
                                <td>{{ $salary->staff->staff_id }}</td>
                                <td>
                                    <a href="{{ route('staff.show', $salary->staff->id) }}">
                                        {{ $salary->staff->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $salary->staff->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::create($salary->year, $salary->month)->format('M Y') }}</td>
                                <td>₹{{ number_format($salary->basic_salary, 2) }}</td>
                                <td>₹{{ number_format($salary->allowances, 2) }}</td>
                                <td>₹{{ number_format($salary->deductions, 2) }}</td>
                                <td><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
                                <td>
                                    @if($salary->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('salaries.slip', $salary->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer"></i> Slip
                                    </a>
                                    @if($salary->payment_status === 'pending')
                                        <form action="{{ route('salaries.mark-paid', $salary->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Mark this salary as paid?')">
                                                <i class="bi bi-check-circle"></i> Mark Paid
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $salaries->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cash-stack" style="font-size: 3rem; color: #ccc;"></i>
                <h4 class="mt-3">No Salary Records Found</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['month', 'year', 'status']))
                        No salary records match your filter criteria.
                    @else
                        Start by processing salaries for this month.
                    @endif
                </p>
                <a href="{{ route('salaries.process') }}" class="btn btn-primary">
                    <i class="bi bi-cash-stack"></i> Process Salary
                </a>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif
@endsection
