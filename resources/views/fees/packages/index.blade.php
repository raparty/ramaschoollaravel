@extends('layouts.app')

@section('title', 'Fee Packages - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Fee Packages</h2>
        <p class="text-muted mb-0">Manage school fee packages</p>
    </div>
    <a href="{{ route('fee-packages.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Fee Package
    </a>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('fee-packages.index') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by package name..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Fee Packages Table -->
<div class="card">
    <div class="card-body">
        @if($packages->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Total Amount</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td><strong>{{ $package->package_name }}</strong></td>
                                <td>
                                    <span class="badge bg-success">₹{{ number_format($package->total_amount, 2) }}</span>
                                </td>
                                <td>{{ $package->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('fee-packages.show', $package) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            👁️
                                        </a>
                                        <a href="{{ route('fee-packages.edit', $package) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            ✏️
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('fee-packages.destroy', $package) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this fee package?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $packages->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">No fee packages found.</p>
                <a href="{{ route('fee-packages.create') }}" class="btn btn-primary">
                    Create First Fee Package
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Info Card -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">💡 About Fee Packages</h6>
    </div>
    <div class="card-body">
        <p class="mb-0">
            Fee packages define the total amount of fees for different classes or categories. 
            You can create different packages for different classes (e.g., Class 1 Fee, Class 10 Fee) 
            and assign them to students during admission.
        </p>
    </div>
</div>
@endsection
