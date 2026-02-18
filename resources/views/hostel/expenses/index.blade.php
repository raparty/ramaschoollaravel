@extends('layouts.app')

@section('title', 'Hostel Expenses - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>💰 Hostel Expenses</h2>
        <p class="text-muted mb-0">Manage student hostel expenses and approvals</p>
    </div>
    <div>
        <a href="{{ route('hostel.expenses.pending') }}" class="btn btn-warning me-2">
            <i class="bi bi-clock-history"></i> Pending Approvals
        </a>
        <a href="{{ route('hostel.expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Expense
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.expenses.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by student or bill no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

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
                            <th>Description</th>
                            <th>Status</th>
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
                                        <span class="badge bg-secondary">{{ $expense->category->category_name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <strong>₹{{ number_format($expense->amount, 2) }}</strong>
                                </td>
                                <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}</td>
                                <td>{{ Str::limit($expense->description ?? '', 40) }}</td>
                                <td>
                                    @if($expense->status === 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($expense->status === 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($expense->status === 'Rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-info">{{ $expense->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.expenses.show', $expense) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($expense->status === 'Pending')
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
                                        @endif
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
                    <i class="bi bi-receipt" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5 class="text-muted">No expenses found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status', 'category_id', 'from_date', 'to_date']))
                        No expenses match your search criteria. Try adjusting your filters.
                    @else
                        Get started by recording your first expense.
                    @endif
                </p>
                <a href="{{ route('hostel.expenses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Record First Expense
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
