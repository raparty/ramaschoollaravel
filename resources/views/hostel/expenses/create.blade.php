@extends('layouts.app')

@section('title', 'New Expense - School ERP')

@section('content')
<div class="mb-4">
    <h2>💰 New Expense</h2>
    <p class="text-muted">Record a new hostel expense</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.expenses.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="wallet_id" class="form-label">Student Wallet <span class="text-danger">*</span></label>
                    <select class="form-select @error('wallet_id') is-invalid @enderror" 
                            id="wallet_id" 
                            name="wallet_id" 
                            required>
                        <option value="">Select Student Wallet</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ old('wallet_id', $walletId) == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->student->student_name ?? 'Unknown' }} (Balance: ₹{{ number_format($wallet->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('wallet_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" 
                            name="category_id" 
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}{{ $category->requires_approval ? ' (Requires Approval)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('amount') is-invalid @enderror" 
                           id="amount" 
                           name="amount" 
                           value="{{ old('amount') }}" 
                           required
                           min="0.01"
                           step="0.01"
                           placeholder="0.00">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('expense_date') is-invalid @enderror" 
                           id="expense_date" 
                           name="expense_date" 
                           value="{{ old('expense_date', date('Y-m-d')) }}" 
                           required>
                    @error('expense_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="bill_number" class="form-label">Bill Number</label>
                    <input type="text" 
                           class="form-control @error('bill_number') is-invalid @enderror" 
                           id="bill_number" 
                           name="bill_number" 
                           value="{{ old('bill_number') }}" 
                           maxlength="50"
                           placeholder="e.g., BILL-001">
                    @error('bill_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3"
                              required
                              placeholder="Enter expense description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Note:</strong> 
                <span id="approval-note">
                    Select a category to see if approval is required.
                </span>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Submit Expense
                </button>
                <a href="{{ route('hostel.expenses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('category_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const approvalNote = document.getElementById('approval-note');
        
        if (selectedOption.text.includes('Requires Approval')) {
            approvalNote.textContent = 'This expense category requires approval before funds are deducted from the wallet.';
        } else if (selectedOption.value) {
            approvalNote.textContent = 'This expense will be automatically approved and deducted from the wallet.';
        } else {
            approvalNote.textContent = 'Select a category to see if approval is required.';
        }
    });
</script>
@endpush
@endsection
