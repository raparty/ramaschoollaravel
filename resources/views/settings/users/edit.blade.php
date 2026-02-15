@extends('layouts.app')

@section('title', 'Edit User Role')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-person-gear"></i> Edit User Role</h2>
            <p class="text-muted">Assign a role to: <strong>{{ $user->full_name ?? $user->user_id }}</strong></p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('settings.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">User ID:</th>
                                <td><strong>{{ $user->user_id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td>{{ $user->full_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Contact:</th>
                                <td>{{ $user->contact_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Current Role:</th>
                                <td>
                                    @if($user->role)
                                        <span class="badge bg-primary">{{ $user->role }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Role Assigned</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <div class="mb-3">
                            <label for="role" class="form-label">Assign Role *</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_name }}" 
                                            {{ old('role', $user->role) === $role->role_name ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                        @if($role->status === 'inactive')
                                            (Inactive)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select a role to assign permissions to this user</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update User Role
                        </button>
                        <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Important</h5>
                </div>
                <div class="card-body">
                    <p><strong>Changing User Role:</strong></p>
                    <ul class="small">
                        <li>User will immediately inherit all permissions from the new role</li>
                        <li>Previous role permissions will be removed</li>
                        <li>User's access to modules will be updated</li>
                        <li>User may need to log out and log back in for changes to take effect</li>
                    </ul>
                </div>
            </div>

            @if($user->role)
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Current Role Details</h5>
                </div>
                <div class="card-body">
                    @php
                        $currentRole = \App\Models\Role::where('role_name', $user->role)->first();
                    @endphp
                    @if($currentRole)
                        <p class="small mb-2"><strong>Role:</strong> {{ $currentRole->role_name }}</p>
                        <p class="small mb-2"><strong>Permissions:</strong> {{ $currentRole->permissions()->count() }}</p>
                        <p class="small mb-0"><strong>Description:</strong> {{ $currentRole->description ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted small mb-0">Role details not available</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endsection
