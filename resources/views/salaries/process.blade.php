@extends('layouts.app')

@section('title', 'Process Salary - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Process Salary</h2>
        <p class="text-muted mb-0">Process individual or bulk salary for staff</p>
    </div>
    <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="row">
    <!-- Process Form -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Salary Details</h5>
                <form action="{{ route('salaries.generate-bulk') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Generate salary for all active staff members?')">
                        <i class="bi bi-lightning"></i> Generate Bulk
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('salaries.store') }}" method="POST" id="salaryForm">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Select Staff <span class="text-danger">*</span></label>
                            <select name="staff_id" id="staffSelect" class="form-select @error('staff_id') is-invalid @enderror" required onchange="loadStaffSalary()">
                                <option value="">-- Select Staff Member --</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" 
                                            data-salary="{{ $member->salary }}"
                                            {{ request('staff_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->staff_id }} - {{ $member->name }} ({{ $member->department->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select @error('month') is-invalid @enderror" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('month', $currentMonth) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <select name="year" class="form-select @error('year') is-invalid @enderror" required>
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ old('year', $currentYear) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                            <input type="number" name="basic_salary" id="basicSalary" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary') }}" step="0.01" required onchange="calculateNet()">
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Allowances</label>
                            <input type="number" name="allowances" id="allowances" class="form-control @error('allowances') is-invalid @enderror" value="{{ old('allowances', 0) }}" step="0.01" onchange="calculateNet()">
                            @error('allowances')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Deductions</label>
                            <input type="number" name="deductions" id="deductions" class="form-control @error('deductions') is-invalid @enderror" value="{{ old('deductions', 0) }}" step="0.01" onchange="calculateNet()">
                            @error('deductions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Net Salary <span class="text-danger">*</span></label>
                            <input type="number" name="net_salary" id="netSalary" class="form-control @error('net_salary') is-invalid @enderror" value="{{ old('net_salary') }}" step="0.01" required readonly>
                            @error('net_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Process Salary
                            </button>
                            <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Processing for:</strong></p>
                <p>{{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}</p>
                
                <hr>
                
                <p><strong>Tips:</strong></p>
                <ul class="small">
                    <li>Select staff to auto-fill basic salary</li>
                    <li>Add allowances and deductions as needed</li>
                    <li>Net salary is calculated automatically</li>
                    <li>Use bulk generate for all staff at once</li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Active Staff:</span>
                    <strong>{{ $staff->count() }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadStaffSalary() {
    const select = document.getElementById('staffSelect');
    const selected = select.options[select.selectedIndex];
    const salary = selected.getAttribute('data-salary');
    
    if (salary) {
        document.getElementById('basicSalary').value = salary;
        calculateNet();
    }
}

function calculateNet() {
    const basic = parseFloat(document.getElementById('basicSalary').value) || 0;
    const allowances = parseFloat(document.getElementById('allowances').value) || 0;
    const deductions = parseFloat(document.getElementById('deductions').value) || 0;
    
    const net = basic + allowances - deductions;
    document.getElementById('netSalary').value = net.toFixed(2);
}
</script>
@endpush

@if(session('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
    </div>
@endif
@endsection
