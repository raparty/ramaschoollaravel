@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-building"></i> Departments</h2>
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Department
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Staff Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td><strong>{{ $department->name }}</strong></td>
                                    <td>{{ $department->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $department->staff()->count() }} Staff
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('departments.edit', $department) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $departments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No departments found</p>
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Department
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
