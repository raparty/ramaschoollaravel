@extends('layouts.app')
@section('title', 'Add Expense')
@section('content')
<div class="container">
    <h2><i class="bi bi-plus"></i> Add Expense</h2>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="mb-3"><label>Amount *</label><input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Date *</label><input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date', date('Y-m-d')) }}" required>@error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Category *</label><select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required><option value="">Select</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach</select>@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Receipt Number</label><input type="text" class="form-control" name="receipt_number" value="{{ old('receipt_number') }}"></div>
                <div class="mb-3"><label>Payment Method *</label><select class="form-select" name="payment_method" required><option value="cash">Cash</option><option value="cheque">Cheque</option><option value="bank">Bank Transfer</option><option value="online">Online</option><option value="card">Card</option></select></div>
                <div class="mb-3"><label>Description</label><textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea></div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-danger">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection