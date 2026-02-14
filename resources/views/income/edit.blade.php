@extends('layouts.app')
@section('title', 'Edit Income')
@section('content')
<div class="container">
    <h2><i class="bi bi-pencil"></i> Edit Income</h2>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('income.update', $income) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3"><label>Amount *</label><input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $income->amount) }}" required>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Date *</label><input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required>@error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Category *</label><select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required><option value="">Select</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $income->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach</select>@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label>Invoice Number</label><input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number', $income->invoice_number) }}"></div>
                <div class="mb-3"><label>Payment Method *</label><select class="form-select" name="payment_method" required>@foreach(['cash', 'cheque', 'bank', 'online', 'card'] as $method)<option value="{{ $method }}" {{ old('payment_method', $income->payment_method) == $method ? 'selected' : '' }}>{{ ucfirst($method) }}</option>@endforeach</select></div>
                <div class="mb-3"><label>Description</label><textarea class="form-control" name="description" rows="3">{{ old('description', $income->description) }}</textarea></div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('income.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection