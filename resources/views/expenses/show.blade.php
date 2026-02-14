@extends('layouts.app')
@section('title', 'Expense Details')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h2><i class="bi bi-info-circle"></i> Expense Details</h2>
        <div>
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3"><strong>Amount:</strong><p class="text-danger fs-4">₹{{ number_format($expense->amount, 2) }}</p></div>
                <div class="col-md-6 mb-3"><strong>Date:</strong><p>{{ $expense->date->format('d M Y') }}</p></div>
                <div class="col-md-6 mb-3"><strong>Category:</strong><p><span class="badge bg-danger">{{ $expense->category->name }}</span></p></div>
                <div class="col-md-6 mb-3"><strong>Receipt Number:</strong><p>{{ $expense->receipt_number ?? 'N/A' }}</p></div>
                <div class="col-md-6 mb-3"><strong>Payment Method:</strong><p><span class="badge bg-info">{{ ucfirst($expense->payment_method) }}</span></p></div>
                <div class="col-md-6 mb-3"><strong>Recorded By:</strong><p>{{ $expense->recorded_by ?? 'System' }}</p></div>
                <div class="col-12 mb-3"><strong>Description:</strong><p>{{ $expense->description ?? 'N/A' }}</p></div>
            </div>
        </div>
    </div>
</div>
@endsection