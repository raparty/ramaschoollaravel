@extends('layouts.app')
@section('title', 'Income')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <h2><i class="bi bi-cash-coin"></i> Income</h2>
                <a href="{{ route('income.create') }}" class="btn btn-success"><i class="bi bi-plus"></i> Add Income</a>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row mb-3">
        <div class="col-md-4"><div class="card text-white bg-success"><div class="card-body"><h5>Total: ₹{{ number_format($totalIncome, 2) }}</h5></div></div></div>
        <div class="col-md-4"><div class="card text-white bg-info"><div class="card-body"><h5>Monthly: ₹{{ number_format($monthlyIncome, 2) }}</h5></div></div></div>
        <div class="col-md-4"><div class="card text-white bg-primary"><div class="card-body"><h5>Yearly: ₹{{ number_format($yearlyIncome, 2) }}</h5></div></div></div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Date</th><th>Category</th><th>Amount</th><th>Invoice</th><th>Payment</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($incomes as $income)
                        <tr>
                            <td>{{ $income->date?->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ $income->category->name }}</td>
                            <td>₹{{ number_format($income->amount, 2) }}</td>
                            <td>{{ $income->invoice_number }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($income->payment_method) }}</span></td>
                            <td>
                                <a href="{{ route('income.show', $income) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('income.edit', $income) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('income.destroy', $income) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No income records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $incomes->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection