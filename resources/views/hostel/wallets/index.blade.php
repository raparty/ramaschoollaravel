@extends('layouts.app')

@section('title', 'Student Wallets - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>💳 Student Imprest Wallets</h2>
        <p class="text-muted mb-0">Manage student hostel expense wallets</p>
    </div>
    <a href="{{ route('hostel.wallets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Wallet
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.wallets.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by student name or reg no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Low Balance</label>
                <select name="low_balance" class="form-select">
                    <option value="">All Wallets</option>
                    <option value="1000" {{ request('low_balance') === '1000' ? 'selected' : '' }}>Below 1000</option>
                    <option value="500" {{ request('low_balance') === '500' ? 'selected' : '' }}>Below 500</option>
                    <option value="100" {{ request('low_balance') === '100' ? 'selected' : '' }}>Below 100</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Wallets Table -->
<div class="card">
    <div class="card-body">
        @if($wallets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Opening Balance</th>
                            <th>Current Balance</th>
                            <th>Total Credited</th>
                            <th>Total Debited</th>
                            <th>Opened Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wallets as $wallet)
                            <tr>
                                <td>
                                    @if($wallet->student)
                                        <div><strong>{{ $wallet->student->student_name }}</strong></div>
                                        <small class="text-muted">{{ $wallet->student->reg_no }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>₹{{ number_format($wallet->opening_balance, 2) }}</td>
                                <td>
                                    <strong class="{{ $wallet->current_balance < 500 ? 'text-danger' : 'text-success' }}">
                                        ₹{{ number_format($wallet->current_balance, 2) }}
                                    </strong>
                                </td>
                                <td>₹{{ number_format($wallet->total_credited, 2) }}</td>
                                <td>₹{{ number_format($wallet->total_debited, 2) }}</td>
                                <td>{{ $wallet->wallet_opened_date ? \Carbon\Carbon::parse($wallet->wallet_opened_date)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($wallet->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.wallets.show', $wallet) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('hostel.wallets.credit.form', $wallet) }}" 
                                           class="btn btn-outline-success" 
                                           title="Credit Amount">
                                            <i class="bi bi-plus-circle"></i>
                                        </a>
                                        <a href="{{ route('hostel.wallets.statement', $wallet) }}" 
                                           class="btn btn-outline-info" 
                                           title="Statement">
                                            <i class="bi bi-file-text"></i>
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
                {{ $wallets->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-wallet2" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5 class="text-muted">No wallets found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'is_active', 'low_balance']))
                        No wallets match your search criteria. Try adjusting your filters.
                    @else
                        Get started by creating your first student wallet.
                    @endif
                </p>
                <a href="{{ route('hostel.wallets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Wallet
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
