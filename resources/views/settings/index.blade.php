@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>⚙️ System Settings</h2>
            <p class="text-muted">Configure system-wide settings, academic sessions, and school information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-gear" style="font-size: 4rem; color: #6c757d;"></i>
                    <h3 class="mt-3">Settings & Configuration</h3>
                    <p class="text-muted">
                        This module manages system settings and configuration including:
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li>• System-wide settings</li>
                        <li>• Academic session management</li>
                        <li>• School information and branding</li>
                        <li>• Role-based access control (RBAC)</li>
                        <li>• User permissions management</li>
                    </ul>
                    <div class="alert alert-info mt-4">
                        <strong>Note:</strong> Settings module features are being configured. 
                        Please contact the administrator for access.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
