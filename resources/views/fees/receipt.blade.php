@extends('layouts.app')

@section('title', 'Fee Receipt - School ERP')

@section('content')
<div class="mb-4 d-print-none">
    <button onclick="window.print()" class="btn btn-primary">
        🖨️ Print Receipt
    </button>
    <a href="{{ route('fees.search') }}" class="btn btn-secondary">
        ← Back to Fee Collection
    </a>
</div>

<!-- Receipt -->
<div class="card">
    <div class="card-body p-5">
        <!-- Header -->
        <div class="text-center mb-4 pb-3 border-bottom">
            <h2 class="mb-1">School ERP System</h2>
            <p class="text-muted mb-0">Fee Payment Receipt</p>
        </div>

        <!-- Receipt Info -->
        <div class="row mb-4">
            <div class="col-6">
                <p class="mb-1"><strong>Receipt No:</strong> {{ $fee->receipt_no }}</p>
                <p class="mb-1"><strong>Payment Date:</strong> {{ $fee->payment_date->format('d M Y') }}</p>
                <p class="mb-0"><strong>Payment Mode:</strong> {{ $fee->payment_mode ?? 'Cash' }}</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1"><strong>Session:</strong> {{ $fee->session }}-{{ $fee->session + 1 }}</p>
                <p class="mb-0"><strong>Term:</strong> {{ $fee->term->term_name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-light p-3 rounded mb-4">
            <h5 class="mb-3">Student Information</h5>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2"><strong>Name:</strong> {{ $fee->student->student_name }}</p>
                    <p class="mb-2"><strong>Reg No:</strong> {{ $fee->student->reg_no }}</p>
                    <p class="mb-0"><strong>Class:</strong> {{ $fee->student->class->name ?? 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-2"><strong>Guardian:</strong> {{ $fee->student->guardian_name ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Phone:</strong> {{ $fee->student->guardian_phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="mb-4">
            <h5 class="mb-3">Payment Details</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th width="200" class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $fee->term->term_name ?? 'Fee Payment' }}</td>
                        <td class="text-end">₹{{ number_format($fee->fees_amount, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <th>Total Paid</th>
                        <th class="text-end">₹{{ number_format($fee->fees_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            @if($fee->remarks)
                <p class="mb-0"><strong>Remarks:</strong> {{ $fee->remarks }}</p>
            @endif
        </div>

        <!-- Fee Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Fee Summary</h5>
                <table class="table table-sm">
                    <tr>
                        <td>Total Fee Package:</td>
                        <td class="text-end">₹{{ number_format($fee->student->admission_fee ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Paid (Including this payment):</td>
                        <td class="text-end text-success">₹{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Remaining Balance:</strong></td>
                        <td class="text-end {{ $pendingBalance > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>₹{{ number_format($pendingBalance, 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="row mt-5 pt-4 border-top">
            <div class="col-6">
                <p class="mb-0">
                    <strong>Received By:</strong><br>
                    ________________________<br>
                    <small class="text-muted">Authorized Signature</small>
                </p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0">
                    <strong>Guardian Signature:</strong><br>
                    ________________________<br>
                    <small class="text-muted">{{ $fee->student->guardian_name ?? 'Parent/Guardian' }}</small>
                </p>
            </div>
        </div>

        <!-- Print Note -->
        <div class="text-center mt-4 text-muted">
            <small>
                This is a computer-generated receipt and does not require a signature when printed.<br>
                For any queries, please contact the school office.
            </small>
        </div>
    </div>
</div>

<!-- Quick Actions (Print Hidden) -->
<div class="mt-4 d-print-none">
    <div class="card">
        <div class="card-body">
            <h6>Quick Actions</h6>
            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-outline-primary">
                    🖨️ Print Receipt
                </button>
                <a href="{{ route('fees.collect', ['registration_no' => $fee->student->reg_no]) }}" class="btn btn-outline-success">
                    💰 Collect More Fees
                </a>
                <a href="{{ route('admissions.show', $fee->student) }}" class="btn btn-outline-info">
                    👁️ View Student Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .d-print-none {
            display: none !important;
        }
        body {
            background-color: white !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .sidebar {
            display: none !important;
        }
        .navbar {
            display: none !important;
        }
        .main-content {
            padding: 0 !important;
        }
    }
</style>
@endpush
