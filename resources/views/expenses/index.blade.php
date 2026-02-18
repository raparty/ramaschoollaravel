@extends('layouts.app')
@section('title', 'Expenses')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <h2><i class="bi bi-wallet2"></i> Expenses</h2>
                <a href="{{ route('expenses.create') }}" class="btn btn-danger"><i class="bi bi-plus"></i> Add Expense</a>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row mb-3">
        <div class="col-md-4"><div class="card text-white bg-danger"><div class="card-body"><h5>Total: ₹{{ number_format($totalExpense, 2) }}</h5></div></div></div>
        <div class="col-md-4"><div class="card text-white bg-warning"><div class="card-body"><h5>Monthly: ₹{{ number_format($monthlyExpense, 2) }}</h5></div></div></div>
        <div class="col-md-4"><div class="card text-white bg-dark"><div class="card-body"><h5>Yearly: ₹{{ number_format($yearlyExpense, 2) }}</h5></div></div></div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Date</th><th>Category</th><th>Amount</th><th>Receipt</th><th>Payment</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date->format('d M Y') }}</td>
                            <td>{{ $expense->category->name }}</td>
                            <td>₹{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->receipt_number }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($expense->payment_method) }}</span></td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No expense records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $expenses->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection