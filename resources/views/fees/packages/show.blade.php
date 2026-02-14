@extends('layouts.app')

@section('title', 'Fee Package Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Fee Package Details</h2>
        <p class="text-muted mb-0">{{ $feePackage->package_name }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('fee-packages.edit', $feePackage) }}" class="btn btn-warning">
            ✏️ Edit
        </a>
        <form method="POST" 
              action="{{ route('fee-packages.destroy', $feePackage) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this fee package?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                🗑️ Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Package Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Package Name:</th>
                            <td><strong>{{ $feePackage->package_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>
                                <h4 class="text-success mb-0">₹{{ number_format($feePackage->total_amount, 2) }}</h4>
                            </td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $feePackage->description ?? 'No description provided' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Students Using This Package</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <em>Student assignment feature coming soon...</em>
                </p>
                <p class="mb-0">
                    This package can be assigned to students during the admission process.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('fee-packages.edit', $feePackage) }}" class="btn btn-outline-primary">
                        ✏️ Edit Package
                    </a>
                    <a href="{{ route('fee-packages.create') }}" class="btn btn-outline-success">
                        ➕ Create New Package
                    </a>
                    <a href="{{ route('fee-packages.index') }}" class="btn btn-outline-secondary">
                        📋 View All Packages
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Record Information</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Created:</strong> {{ $feePackage->created_at->format('d M Y, h:i A') }}<br>
                    <strong>Last Updated:</strong> {{ $feePackage->updated_at->format('d M Y, h:i A') }}
                </small>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('fee-packages.index') }}" class="btn btn-secondary">
        ← Back to List
    </a>
</div>
@endsection
