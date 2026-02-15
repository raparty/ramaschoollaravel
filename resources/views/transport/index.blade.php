@extends('layouts.app')

@section('title', 'Transport Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🚌 Transport Management</h2>
            <p class="text-muted">Manage school transport, vehicles, routes, and student assignments</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Routes Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z M13 17.94v-14l5.47 9.06-5.47 4.94z M11 3.94l-5.47 9.06 5.47 4.94V3.94z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Routes</h5>
                    <p class="card-text text-muted small">
                        Transport routes with destinations
                    </p>
                    <a href="{{ route('transport.routes.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Drivers Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Drivers</h5>
                    <p class="card-text text-muted small">
                        Driver details and assignments
                    </p>
                    <a href="{{ route('transport.drivers.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Vehicles</h5>
                    <p class="card-text text-muted small">
                        Vehicles and their capacities
                    </p>
                    <a href="{{ route('transport.vehicles.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <!-- Student Assignments Card -->
        <div class="col-md-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="64" height="64" fill="currentColor" style="color: #0078d4;" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Students</h5>
                    <p class="card-text text-muted small">
                        Student transport assignments
                    </p>
                    <a href="{{ route('transport.students.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Manage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats (Optional) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Transport Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\TransportRoute::count() }}</h3>
                                <p class="text-muted mb-0">Routes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\TransportDriver::count() }}</h3>
                                <p class="text-muted mb-0">Drivers</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\TransportVehicle::count() }}</h3>
                                <p class="text-muted mb-0">Vehicles</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ \App\Models\TransportStudentAssignment::count() }}</h3>
                                <p class="text-muted mb-0">Students</p>
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
