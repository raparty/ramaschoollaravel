@extends('layouts.app')

@section('title', 'Pending Fees Report - School ERP')

@section('content')
<div class="mb-4">
    <h2>Pending Fees Report</h2>
    <p class="text-muted">Students with pending fee payments</p>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('fees.pending') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Term</label>
                <select name="fees_term" class="form-select">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ request('fees_term') == $term->id ? 'selected' : '' }}>
                            {{ $term->term_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Pending Fees Table -->
<div class="card">
    <div class="card-body">
        @if(count($pendingData) > 0)
            <div class="alert alert-info">
                <strong>{{ count($pendingData) }}</strong> student(s) with pending fees totaling 
                <strong>₹{{ number_format(array_sum(array_column($pendingData, 'pending')), 2) }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Guardian</th>
                            <th>Phone</th>
                            <th class="text-end">Total Package</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Pending</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingData as $data)
                            <tr>
                                <td><strong>{{ $data['student']->reg_no }}</strong></td>
                                <td>{{ $data['student']->student_name }}</td>
                                <td>{{ $data['student']->class->name ?? 'N/A' }}</td>
                                <td>{{ $data['student']->guardian_name ?? '-' }}</td>
                                <td>
                                    @if($data['student']->guardian_phone)
                                        <a href="tel:{{ $data['student']->guardian_phone }}">
                                            {{ $data['student']->guardian_phone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">₹{{ number_format($data['total_package'], 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($data['total_paid'], 2) }}</td>
                                <td class="text-end text-danger"><strong>₹{{ number_format($data['pending'], 2) }}</strong></td>
                                <td>
                                    <a href="{{ route('fees.collect', ['registration_no' => $data['student']->reg_no]) }}" 
                                       class="btn btn-sm btn-primary">
                                        Collect
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="7" class="text-end">Total Pending:</th>
                            <th class="text-end text-danger">
                                ₹{{ number_format(array_sum(array_column($pendingData, 'pending')), 2) }}
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Export Options -->
            <div class="mt-3">
                <button class="btn btn-outline-success" onclick="window.print()">
                    🖨️ Print Report
                </button>
                <button class="btn btn-outline-primary" onclick="exportToCSV()">
                    📥 Export to CSV
                </button>
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-success h4">✅ No Pending Fees!</p>
                <p class="text-muted">All students have paid their fees.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportToCSV() {
        // Simple CSV export functionality
        let csv = 'Reg No,Student Name,Class,Guardian,Phone,Total Package,Paid,Pending\n';
        
        @foreach($pendingData as $data)
            csv += '"{{ $data['student']->reg_no }}",' +
                   '"{{ $data['student']->student_name }}",' +
                   '"{{ $data['student']->class->name ?? 'N/A' }}",' +
                   '"{{ $data['student']->guardian_name ?? '' }}",' +
                   '"{{ $data['student']->guardian_phone ?? '' }}",' +
                   '"{{ $data['total_package'] }}",' +
                   '"{{ $data['total_paid'] }}",' +
                   '"{{ $data['pending'] }}"\n';
        @endforeach
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'pending-fees-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
