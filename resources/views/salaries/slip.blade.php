@extends('layouts.app')

@section('title', 'Salary Slip - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h2>Salary Slip</h2>
        <p class="text-muted mb-0">{{ $salary->staff->name }} - {{ \Carbon\Carbon::create($salary->year, $salary->month)->format('F Y') }}</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card" id="salarySlip">
    <div class="card-body">
        <!-- School Header -->
        <div class="text-center mb-4">
            <h3><strong>{{ config('app.name', 'School ERP System') }}</strong></h3>
            <p class="mb-0">Salary Slip for {{ \Carbon\Carbon::create($salary->year, $salary->month)->format('F Y') }}</p>
        </div>
        
        <hr>
        
        <!-- Staff Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="150"><strong>Staff ID:</strong></td>
                        <td>{{ $salary->staff->staff_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $salary->staff->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td>{{ $salary->staff->department->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="150"><strong>Position:</strong></td>
                        <td>{{ $salary->staff->position->title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Joining Date:</strong></td>
                        <td>{{ $salary->staff->joining_date ? $salary->staff->joining_date->format('d M, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Slip Generated:</strong></td>
                        <td>{{ $salary->created_at->format('d M, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Salary Details -->
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Earnings</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end"><strong>₹{{ number_format($salary->basic_salary, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Allowances</td>
                            <td class="text-end"><strong>₹{{ number_format($salary->allowances, 2) }}</strong></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Total Earnings</strong></td>
                            <td class="text-end"><strong>₹{{ number_format($salary->basic_salary + $salary->allowances, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">Deductions</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Total Deductions</td>
                            <td class="text-end"><strong>₹{{ number_format($salary->deductions, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr class="table-danger">
                            <td><strong>Total Deductions</strong></td>
                            <td class="text-end"><strong>₹{{ number_format($salary->deductions, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Net Salary -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Net Salary</h4>
                            <h3 class="mb-0">₹{{ number_format($salary->net_salary, 2) }}</h3>
                        </div>
                        <p class="mb-0 small">In Words: {{ ucwords(numberToWords($salary->net_salary)) }} Rupees Only</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="row mt-4">
            <div class="col-md-6">
                <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $salary->payment_method)) }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Payment Status:</strong> 
                    @if($salary->payment_status === 'paid')
                        <span class="badge bg-success">Paid</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </p>
            </div>
            @if($salary->payment_date)
                <div class="col-md-6">
                    <p><strong>Payment Date:</strong> {{ $salary->payment_date->format('d M, Y') }}</p>
                </div>
            @endif
            @if($salary->notes)
                <div class="col-12">
                    <p><strong>Notes:</strong> {{ $salary->notes }}</p>
                </div>
            @endif
        </div>
        
        <hr class="mt-5">
        
        <!-- Footer -->
        <div class="row mt-4">
            <div class="col-6">
                <p class="text-center">
                    ___________________<br>
                    <small>Staff Signature</small>
                </p>
            </div>
            <div class="col-6">
                <p class="text-center">
                    ___________________<br>
                    <small>Authorized Signature</small>
                </p>
            </div>
        </div>
        
        <p class="text-center text-muted small mt-4">
            This is a computer-generated slip and does not require a signature.
        </p>
    </div>
</div>

<style>
@media print {
    .d-print-none {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    #salarySlip {
        border: none;
        box-shadow: none;
    }
    
    .card {
        page-break-inside: avoid;
    }
}
</style>

@php
function numberToWords($number) {
    $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    
    if ($number < 10) return $ones[$number];
    if ($number < 20) return $teens[$number - 10];
    if ($number < 100) return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
    if ($number < 1000) return $ones[intval($number / 100)] . ' hundred ' . numberToWords($number % 100);
    if ($number < 100000) return numberToWords(intval($number / 1000)) . ' thousand ' . numberToWords($number % 1000);
    if ($number < 10000000) return numberToWords(intval($number / 100000)) . ' lakh ' . numberToWords($number % 100000);
    
    return numberToWords(intval($number / 10000000)) . ' crore ' . numberToWords($number % 10000000);
}
@endphp
@endsection
