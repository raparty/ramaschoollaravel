@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-key-fill"></i> Create New Permission</h2>
            <p class="text-muted">Add a new system permission</p>
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
            <form action="{{ route('settings.permissions.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="module" class="form-label">Module *</label>
                            <input type="text" class="form-control @error('module') is-invalid @enderror" 
                                   id="module" name="module" value="{{ old('module') }}" 
                                   placeholder="e.g., students, fees, library" required list="existing-modules">
                            <datalist id="existing-modules">
                                @foreach($modules as $mod)
                                    <option value="{{ $mod }}">
                                @endforeach
                            </datalist>
                            @error('module')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter module name (lowercase, singular)</small>
                        </div>

                        <div class="mb-3">
                            <label for="action" class="form-label">Action *</label>
                            <select class="form-select @error('action') is-invalid @enderror" 
                                    id="action" name="action" required>
                                <option value="">-- Select Action --</option>
                                <option value="view" {{ old('action') === 'view' ? 'selected' : '' }}>View</option>
                                <option value="create" {{ old('action') === 'create' ? 'selected' : '' }}>Create</option>
                                <option value="edit" {{ old('action') === 'edit' ? 'selected' : '' }}>Edit</option>
                                <option value="delete" {{ old('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                                <option value="manage" {{ old('action') === 'manage' ? 'selected' : '' }}>Manage</option>
                            </select>
                            @error('action')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="submodule" class="form-label">Submodule (Optional)</label>
                            <input type="text" class="form-control @error('submodule') is-invalid @enderror" 
                                   id="submodule" name="submodule" value="{{ old('submodule') }}"
                                   placeholder="e.g., reports, categories">
                            @error('submodule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional specific feature within the module</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Create Permission
                        </button>
                        <a href="{{ route('settings.permissions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <p><strong>Common Actions:</strong></p>
                    <ul class="small">
                        <li><strong>view:</strong> Read/view records</li>
                        <li><strong>create:</strong> Add new records</li>
                        <li><strong>edit:</strong> Modify existing records</li>
                        <li><strong>delete:</strong> Remove records</li>
                        <li><strong>manage:</strong> Full control</li>
                    </ul>
                    <hr>
                    <p><strong>Example:</strong></p>
                    <p class="small mb-0">
                        <strong>Module:</strong> students<br>
                        <strong>Action:</strong> view<br>
                        <strong>Result:</strong> "View Students" permission
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endsection
