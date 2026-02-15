@extends('layouts.app')

@section('title', 'View Role')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-shield-check"></i> Role Details: {{ $role->role_name }}</h2>
            <p class="text-muted">View role information and assigned permissions</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('settings.roles.edit', $role) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit Role
            </a>
            <a href="{{ route('settings.roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Role Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Role Name:</th>
                            <td><strong>{{ $role->role_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $role->description ?? 'No description' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($role->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Permissions:</th>
                            <td><span class="badge bg-info">{{ $role->permissions->count() }}</span></td>
                        </tr>
                        <tr>
                            <th>Users Count:</th>
                            <td><span class="badge bg-primary">{{ $usersCount }}</span></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $role->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $role->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Permissions</h5>
                </div>
                <div class="card-body">
                    @if($role->permissions->isEmpty())
                        <p class="text-muted text-center py-3">No permissions assigned to this role.</p>
                    @else
                        <div class="row">
                            @foreach($role->permissions->groupBy('module') as $module => $perms)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>{{ ucfirst($module) }}</strong>
                                        <span class="badge bg-secondary float-end">{{ $perms->count() }}</span>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            @foreach($perms as $permission)
                                            <li class="mb-1">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                {{ ucfirst($permission->action) }}
                                                @if($permission->submodule)
                                                    <small class="text-muted">({{ $permission->submodule }})</small>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('settings.roles.edit', $role) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Role
                        </a>
                        <a href="{{ route('settings.users.index', ['role' => $role->role_name]) }}" class="btn btn-info">
                            <i class="bi bi-people"></i> View Users ({{ $usersCount }})
                        </a>
                        <form action="{{ route('settings.roles.destroy', $role) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" {{ $usersCount > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i> Delete Role
                            </button>
                        </form>
                        @if($usersCount > 0)
                        <small class="text-muted text-center">Cannot delete role with assigned users</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
