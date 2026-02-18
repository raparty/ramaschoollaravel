@extends('layouts.app')

@section('title', 'Pending Expense Approvals - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>💰 Pending Expense Approvals</h2>
        <p class="text-muted mb-0">Review and approve pending hostel expenses</p>
    </div>
    <div>
        <a href="{{ route('hostel.expenses.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to All Expenses
        </a>
    </div>
</div>

<!-- Pending Count Alert -->
@if($expenses->count() > 0)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            You have <strong>{{ $expenses->total() }}</strong> pending expense(s) awaiting approval.
        </div>
    </div>
@endif

<!-- Expenses Table -->
<div class="card">
    <div class="card-body">
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Bill Number</th>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Expense Date</th>
                            <th>Submitted By</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>
                                    <strong>{{ $expense->bill_number ?? '-' }}</strong>
                                </td>
                                <td>
                                    @if($expense->wallet && $expense->wallet->student)
                                        <div>{{ $expense->wallet->student->student_name }}</div>
                                        <small class="text-muted">{{ $expense->wallet->student->reg_no }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($expense->category)
                                        <span class="badge bg-secondary">{{ $expense->category->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-danger">₹{{ number_format($expense->amount, 2) }}</strong>
                                </td>
                                <td>{{ $expense->expense_date ? $expense->expense_date->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($expense->submitter)
                                        <div>{{ $expense->submitter->name }}</div>
                                        <small class="text-muted">{{ $expense->submitter->employee_code }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ Str::limit($expense->description ?? '', 40) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.expenses.show', $expense) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('hostel.expenses.approve.form', $expense) }}" 
                                           class="btn btn-outline-success" 
                                           title="Approve">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                        <a href="{{ route('hostel.expenses.reject.form', $expense) }}" 
                                           class="btn btn-outline-danger" 
                                           title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                </div>
                <h5 class="text-muted">No Pending Approvals</h5>
                <p class="text-muted">
                    All expenses have been reviewed. Great job!
                </p>
                <a href="{{ route('hostel.expenses.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> View All Expenses
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
