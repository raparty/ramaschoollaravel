@extends('layouts.app')
@section('title', 'Account Reports')
@section('content')
<div class="container-fluid">
    <h2><i class="bi bi-graph-up"></i> Account Reports</h2>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="bi bi-pie-chart"></i> Income vs Expense Summary</h5>
                </div>
                <div class="card-body">
                    <p>View comprehensive summary of income and expenses with profit/loss calculation and category breakdown.</p>
                    <form action="{{ route('reports.accounts.summary') }}" method="GET">
                        <div class="mb-3"><label>Start Date</label><input type="date" class="form-control" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"></div>
                        <div class="mb-3"><label>End Date</label><input type="date" class="form-control" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"></div>
                        <button type="submit" class="btn btn-primary">Generate Summary</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5><i class="bi bi-list-ul"></i> Detailed Transaction Report</h5>
                </div>
                <div class="card-body">
                    <p>View detailed listing of all income and expense transactions with filtering options.</p>
                    <form action="{{ route('reports.accounts.details') }}" method="GET">
                        <div class="mb-3"><label>Start Date</label><input type="date" class="form-control" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"></div>
                        <div class="mb-3"><label>End Date</label><input type="date" class="form-control" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"></div>
                        <button type="submit" class="btn btn-success">Generate Details</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection