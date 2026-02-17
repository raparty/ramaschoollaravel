@extends('layouts.app')

@section('title', 'Hostel Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🏠 Hostel Management</h2>
            <p class="text-muted">Manage hostels, rooms, student allocations, wardens, and expenses</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Hostels Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Hostels</h5>
                    <p class="card-text text-muted small">
                        Manage hostel buildings and blocks
                    </p>
                    <a href="{{ route('hostel.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Rooms Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M7 14c-1.66 0-3 1.34-3 3 0 1.31-1.16 2-2 2 .92 1.22 2.49 2 4 2 2.21 0 4-1.79 4-4 0-1.66-1.34-3-3-3zm13.71-9.37l-1.34-1.34a.996.996 0 0 0-1.41 0L9 12.25 11.75 15l8.96-8.96c.39-.39.39-1.02 0-1.41z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Rooms</h5>
                    <p class="card-text text-muted small">
                        Manage rooms, beds, and furniture
                    </p>
                    <a href="{{ route('hostel.rooms.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Student Allocations Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Allocations</h5>
                    <p class="card-text text-muted small">
                        Student check-in and check-out
                    </p>
                    <a href="{{ route('hostel.allocations.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Wardens Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Wardens</h5>
                    <p class="card-text text-muted small">
                        Manage hostel wardens and assignments
                    </p>
                    <a href="{{ route('hostel.wardens.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Second row of cards -->
    <div class="row g-4 mt-2">
        <!-- Imprest Wallets Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Wallets</h5>
                    <p class="card-text text-muted small">
                        Imprest wallet management
                    </p>
                    <a href="{{ route('hostel.wallets.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Expenses Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zm-9-1c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm13-6v11c0 1.1-.9 2-2 2H4v-2h17V7h2z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Expenses</h5>
                    <p class="card-text text-muted small">
                        Track and approve hostel expenses
                    </p>
                    <a href="{{ route('hostel.expenses.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Hostel Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\Hostel::count() }}</h3>
                                <p class="text-muted mb-0">Hostels</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\HostelRoom::count() }}</h3>
                                <p class="text-muted mb-0">Rooms</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\HostelBed::count() }}</h3>
                                <p class="text-muted mb-0">Beds</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\HostelStudentAllocation::where('status', 'Active')->count() }}</h3>
                                <p class="text-muted mb-0">Active Students</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\HostelWarden::where('is_active', true)->count() }}</h3>
                                <p class="text-muted mb-0">Active Wardens</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\HostelExpense::where('status', 'Pending')->count() }}</h3>
                                <p class="text-muted mb-0">Pending Expenses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
