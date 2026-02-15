@extends('layouts.app')

@section('title', 'Positions')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-person-badge"></i> Positions</h2>
                <a href="{{ route('positions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Position
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
            @if($positions->count() > 0)
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
                            @foreach($positions as $position)
                                <tr>
                                    <td><strong>{{ $position->name }}</strong></td>
                                    <td>{{ $position->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $position->staff_count }} Staff
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('positions.edit', $position) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('positions.destroy', $position) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this position?');">
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
                    {{ $positions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No positions found</p>
                    <a href="{{ route('positions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Position
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
