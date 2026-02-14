@extends('layouts.app')

@section('title', 'Collect Fee - School ERP')

@section('content')
<div class="mb-4">
    <h2>Collect Fee</h2>
    <p class="text-muted">Record fee payment for {{ $student->student_name }}</p>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Student Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Name:</strong> {{ $student->student_name }}</p>
                        <p class="mb-2"><strong>Reg No:</strong> {{ $student->reg_no }}</p>
                        <p class="mb-0"><strong>Class:</strong> {{ $student->class->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Guardian:</strong> {{ $student->guardian_name ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Phone:</strong> {{ $student->guardian_phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collection Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fee Payment Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('fees.store') }}">
                    @csrf
                    <input type="hidden" name="registration_no" value="{{ $student->reg_no }}">

                    <div class="mb-3">
                        <label for="fees_term" class="form-label">Fee Term <span class="text-danger">*</span></label>
                        <select class="form-select @error('fees_term') is-invalid @enderror" 
                                id="fees_term" 
                                name="fees_term" 
                                required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('fees_term') == $term->id ? 'selected' : '' }}>
                                    {{ $term->term_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fees_term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="fees_amount" class="form-label">Fee Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('fees_amount') is-invalid @enderror" 
                               id="fees_amount" 
                               name="fees_amount" 
                               value="{{ old('fees_amount') }}" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               required>
                        @error('fees_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_mode') is-invalid @enderror" 
                                id="payment_mode" 
                                name="payment_mode" 
                                required>
                            <option value="">Select Payment Mode</option>
                            <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Online" {{ old('payment_mode') == 'Online' ? 'selected' : '' }}>Online Transfer</option>
                            <option value="Cheque" {{ old('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Card" {{ old('payment_mode') == 'Card' ? 'selected' : '' }}>Card</option>
                        </select>
                        @error('payment_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                  id="remarks" 
                                  name="remarks" 
                                  rows="2"
                                  placeholder="Additional notes (optional)">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('fees.search') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Collect Fee & Generate Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Fee Summary -->
        <div class="card mb-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0">Fee Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td>Total Package:</td>
                        <td class="text-end"><strong>₹{{ number_format($student->admission_fee ?? 0, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Already Paid:</td>
                        <td class="text-end text-success">₹{{ number_format($student->fees->sum('fees_amount'), 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Pending Balance:</strong></td>
                        <td class="text-end text-danger">
                            <strong>₹{{ number_format($pendingBalance, 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Payments</h6>
            </div>
            <div class="card-body">
                @if($paymentHistory->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($paymentHistory->take(5) as $payment)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">{{ $payment->term->term_name ?? 'N/A' }}</small>
                                    <strong class="text-success">₹{{ number_format($payment->fees_amount, 2) }}</strong>
                                </div>
                                <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0 small">No previous payments</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
