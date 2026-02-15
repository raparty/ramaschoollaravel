@extends('layouts.app')

@section('title', 'Manage Permissions')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-key"></i> Permissions Management</h2>
            <p class="text-muted">View and manage system permissions</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('settings.permissions.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Create New Permission
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($grouped->isEmpty())
                <p class="text-center text-muted py-5">No permissions found. Create your first permission!</p>
            @else
                <div class="row">
                    @foreach($grouped as $module => $permissions)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-box"></i> {{ ucfirst($module) }} Module
                                    <span class="badge bg-light text-dark float-end">{{ $permissions->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Submodule</th>
                                                <th>Description</th>
                                                <th width="80">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissions as $permission)
                                            <tr>
                                                <td><strong>{{ ucfirst($permission->action) }}</strong></td>
                                                <td>{{ $permission->submodule ?? '-' }}</td>
                                                <td><small>{{ $permission->description ?? '-' }}</small></td>
                                                <td>
                                                    <form action="{{ route('settings.permissions.destroy', $permission) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this permission?');" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> About Permissions</h5>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Permission Structure:</strong></p>
            <ul>
                <li><strong>Module:</strong> The main module (e.g., students, fees, library)</li>
                <li><strong>Action:</strong> The action type (e.g., view, create, edit, delete)</li>
                <li><strong>Submodule:</strong> Optional specific feature within a module</li>
            </ul>
            <p class="mb-0 text-muted">
                <i class="bi bi-exclamation-triangle"></i> 
                Permissions are grouped by module and assigned to roles. Users inherit permissions from their assigned role.
            </p>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endsection
