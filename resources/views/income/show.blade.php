@extends('layouts.app')
@section('title', 'Income Details')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h2><i class="bi bi-info-circle"></i> Income Details</h2>
        <div>
            <a href="{{ route('income.edit', $income) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('income.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3"><strong>Amount:</strong><p class="text-success fs-4">₹{{ number_format($income->amount, 2) }}</p></div>
                <div class="col-md-6 mb-3"><strong>Date:</strong><p>{{ $income->date?->format('d M Y') ?? 'N/A' }}</p></div>
                <div class="col-md-6 mb-3"><strong>Category:</strong><p><span class="badge bg-success">{{ $income->category->name }}</span></p></div>
                <div class="col-md-6 mb-3"><strong>Invoice Number:</strong><p>{{ $income->invoice_number ?? 'N/A' }}</p></div>
                <div class="col-md-6 mb-3"><strong>Payment Method:</strong><p><span class="badge bg-info">{{ ucfirst($income->payment_method) }}</span></p></div>
                <div class="col-md-6 mb-3"><strong>Recorded By:</strong><p>{{ $income->recorded_by ?? 'System' }}</p></div>
                <div class="col-12 mb-3"><strong>Description:</strong><p>{{ $income->description ?? 'N/A' }}</p></div>
            </div>
        </div>
    </div>
</div>
@endsection