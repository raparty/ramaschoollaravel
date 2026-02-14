@extends('layouts.app')
@section('title', 'Account Summary Report')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h2><i class="bi bi-pie-chart"></i> Income vs Expense Summary</h2>
        <div>
            <a href="{{ route('reports.accounts.export-csv', request()->all()) }}" class="btn btn-success"><i class="bi bi-download"></i> Export CSV</a>
            <button onclick="window.print()" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</button>
            <a href="{{ route('reports.accounts.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><h6>Total Income</h6><h4>₹{{ number_format($totalIncome, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-danger"><div class="card-body"><h6>Total Expense</h6><h4>₹{{ number_format($totalExpense, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-{{ $profitLoss >= 0 ? 'primary' : 'warning' }}"><div class="card-body"><h6>Profit/Loss</h6><h4>₹{{ number_format(abs($profitLoss), 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-info"><div class="card-body"><h6>Period</h6><h6>{{ request('start_date') }} to {{ request('end_date') }}</h6></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white"><h5>Income by Category</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Category</th><th>Amount</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($incomeByCategory as $item)
                            <tr>
                                <td>{{ $item->category }}</td>
                                <td>₹{{ number_format($item->total, 2) }}</td>
                                <td>{{ $totalIncome > 0 ? number_format(($item->total / $totalIncome) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white"><h5>Expense by Category</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Category</th><th>Amount</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($expenseByCategory as $item)
                            <tr>
                                <td>{{ $item->category }}</td>
                                <td>₹{{ number_format($item->total, 2) }}</td>
                                <td>{{ $totalExpense > 0 ? number_format(($item->total / $totalExpense) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>@media print { .btn, .no-print { display: none !important; }}</style>
@endsection