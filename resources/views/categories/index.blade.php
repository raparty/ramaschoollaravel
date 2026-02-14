@extends('layouts.app')

@section('title', 'Account Categories')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-list-ul"></i> Account Categories</h2>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Category
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

    <!-- Type Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button">
                <i class="bi bi-cash-coin text-success"></i> Income Categories
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense" type="button">
                <i class="bi bi-wallet2 text-danger"></i> Expense Categories
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Income Categories Tab -->
        <div class="tab-pane fade show active" id="income" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @php
                        $incomeCategories = $categories->where('type', 'income');
                    @endphp
                    
                    @if($incomeCategories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeCategories as $category)
                                        <tr>
                                            <td><strong>{{ $category->name }}</strong></td>
                                            <td>{{ $category->description ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                                title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-{{ $category->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">No income categories found</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add First Income Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense Categories Tab -->
        <div class="tab-pane fade" id="expense" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @php
                        $expenseCategories = $categories->where('type', 'expense');
                    @endphp
                    
                    @if($expenseCategories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseCategories as $category)
                                        <tr>
                                            <td><strong>{{ $category->name }}</strong></td>
                                            <td>{{ $category->description ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                                title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-{{ $category->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">No expense categories found</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add First Expense Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
