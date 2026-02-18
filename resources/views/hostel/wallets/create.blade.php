@extends('layouts.app')

@section('title', 'New Wallet - School ERP')

@section('content')
<div class="mb-4">
    <h2>💳 New Student Wallet</h2>
    <p class="text-muted">Create a new hostel imprest wallet for a student</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.wallets.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                    <select class="form-select @error('student_id') is-invalid @enderror" 
                            id="student_id" 
                            name="student_id" 
                            required>
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->student_name }} ({{ $student->reg_no }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($students->isEmpty())
                        <small class="text-muted">No students available. All active students may already have wallets.</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="wallet_opened_date" class="form-label">Wallet Opened Date <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('wallet_opened_date') is-invalid @enderror" 
                           id="wallet_opened_date" 
                           name="wallet_opened_date" 
                           value="{{ old('wallet_opened_date', date('Y-m-d')) }}" 
                           required>
                    @error('wallet_opened_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="opening_balance" class="form-label">Opening Balance (₹) <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('opening_balance') is-invalid @enderror" 
                           id="opening_balance" 
                           name="opening_balance" 
                           value="{{ old('opening_balance', 0) }}" 
                           required
                           min="0"
                           step="0.01"
                           placeholder="0.00">
                    @error('opening_balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter the initial amount to credit to this wallet</small>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Note:</strong> 
                The wallet will be created as active, and the opening balance will be set as the current balance.
                You can credit additional funds or manage expenses after wallet creation.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Wallet
                </button>
                <a href="{{ route('hostel.wallets.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
