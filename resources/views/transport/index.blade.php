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

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bus-front" style="font-size: 4rem; color: #6c757d;"></i>
                    <h3 class="mt-3">Transport Module</h3>
                    <p class="text-muted">
                        This module manages school transportation including:
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li>• Vehicle fleet management</li>
                        <li>• Route planning and management</li>
                        <li>• Student transport assignments</li>
                        <li>• Transport fee collection</li>
                        <li>• Driver and conductor management</li>
                    </ul>
                    <div class="alert alert-info mt-4">
                        <strong>Note:</strong> Transport module features are being configured. 
                        Please contact the administrator for access.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
