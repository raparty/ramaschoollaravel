@extends('layouts.app')

@section('title', 'Transport Routes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🚏 Transport Routes</h2>
            <p class="text-muted">Manage transport routes and destinations</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('transport.routes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Route
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transport.routes.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by route name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('transport.routes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="card">
        <div class="card-body">
            @if($routes->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">No routes found. Click "Add New Route" to create one.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No.</th>
                                <th>Route Name / Destination</th>
                                <th style="width: 200px;">Monthly Cost</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $index => $route)
                            <tr>
                                <td>{{ $routes->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $route->route_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-success" style="font-size: 0.95rem;">{{ $route->formatted_cost }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transport.routes.edit', $route) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('transport.routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this route?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $routes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
