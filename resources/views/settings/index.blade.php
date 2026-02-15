@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>⚙️ System Settings</h2>
            <p class="text-muted">Role-Based Access Control (RBAC) & System Configuration</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-shield-check" style="font-size: 2.5rem; color: #0078d4;"></i>
                    <h3 class="mt-2">{{ $stats['roles_count'] }}</h3>
                    <p class="text-muted mb-0">Total Roles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-key" style="font-size: 2.5rem; color: #28a745;"></i>
                    <h3 class="mt-2">{{ $stats['permissions_count'] }}</h3>
                    <p class="text-muted mb-0">Total Permissions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people" style="font-size: 2.5rem; color: #ff9800;"></i>
                    <h3 class="mt-2">{{ $stats['users_count'] }}</h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; color: #17a2b8;"></i>
                    <h3 class="mt-2">{{ $stats['active_roles'] }}</h3>
                    <p class="text-muted mb-0">Active Roles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-shield-check" style="font-size: 2.5rem; color: #0078d4;"></i>
                        <h4 class="ms-3 mb-0">Role Management</h4>
                    </div>
                    <p class="text-muted">Create and manage user roles, assign permissions to roles.</p>
                    <a href="{{ route('settings.roles.index') }}" class="btn btn-primary">
                        <i class="bi bi-gear"></i> Manage Roles
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-key" style="font-size: 2.5rem; color: #28a745;"></i>
                        <h4 class="ms-3 mb-0">Permissions</h4>
                    </div>
                    <p class="text-muted">View and manage system permissions for different modules.</p>
                    <a href="{{ route('settings.permissions.index') }}" class="btn btn-success">
                        <i class="bi bi-key"></i> Manage Permissions
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-people" style="font-size: 2.5rem; color: #ff9800;"></i>
                        <h4 class="ms-3 mb-0">User Management</h4>
                    </div>
                    <p class="text-muted">Assign roles to users and manage user access levels.</p>
                    <a href="{{ route('settings.users.index') }}" class="btn btn-warning text-white">
                        <i class="bi bi-person-gear"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Alert -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> About RBAC</h5>
                <p class="mb-0">
                    Role-Based Access Control (RBAC) restricts system access based on user roles. 
                    Users are assigned roles, and roles are granted permissions to perform specific actions within the system.
                    This ensures that users can only access features and data relevant to their responsibilities.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Add Bootstrap Icons CDN if not already included -->

@endsection

