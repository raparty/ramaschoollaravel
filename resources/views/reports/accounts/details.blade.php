@extends('layouts.app')
@section('title', 'Detailed Account Report')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h2><i class="bi bi-list-ul"></i> Detailed Transaction Report</h2>
        <div>
            <a href="{{ route('reports.accounts.export-csv', request()->all()) }}" class="btn btn-success"><i class="bi bi-download"></i> Export CSV</a>
            <button onclick="window.print()" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</button>
            <a href="{{ route('reports.accounts.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Period: {{ request('start_date') }} to {{ request('end_date') }}</p>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Reference</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trans)
                        <tr>
                            <td>{{ $trans->date?->format('d M Y') ?? 'N/A' }}</td>
                            <td><span class="badge bg-{{ $trans->type == 'income' ? 'success' : 'danger' }}">{{ ucfirst($trans->type) }}</span></td>
                            <td>{{ $trans->category }}</td>
                            <td>{{ $trans->description ?? '-' }}</td>
                            <td>{{ $trans->reference ?? '-' }}</td>
                            <td class="text-{{ $trans->type == 'income' ? 'success' : 'danger' }}">{{ $trans->type == 'income' ? '+' : '-' }}₹{{ number_format($trans->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Total Income:</td>
                            <td class="text-success">₹{{ number_format($totalIncome, 2) }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Total Expense:</td>
                            <td class="text-danger">₹{{ number_format($totalExpense, 2) }}</td>
                        </tr>
                        <tr class="fw-bold bg-light">
                            <td colspan="5" class="text-end">Net:</td>
                            <td class="text-{{ $totalIncome - $totalExpense >= 0 ? 'success' : 'danger' }}">₹{{ number_format(abs($totalIncome - $totalExpense), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<style>@media print { .btn, .no-print { display: none !important; } table { font-size: 12px; }}</style>
@endsection