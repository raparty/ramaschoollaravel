<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | School ERP System</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --fluent-azure: #0078D4;
            --fluent-azure-dark: #106EBE;
            --bg-page: #F3F2F1;
            --text-primary: #323130;
            --text-secondary: #605E5C;
            --border-color: #EDEBE9;
        }

        body {
            background: var(--bg-page);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 0 20px;
        }

        .login-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-badge {
            background: var(--fluent-azure);
            color: #fff;
            display: inline-block;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .welcome-text h4 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 26px;
        }

        .welcome-text p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 4px;
            padding: 12px 16px;
            border: 1px solid #D1D1D1;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--fluent-azure);
            box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1);
        }

        .btn-azure {
            background: var(--fluent-azure);
            color: #fff;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-azure:hover {
            background: var(--fluent-azure-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 120, 212, 0.3);
        }

        .btn-azure:active {
            transform: translateY(0);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .remember-me label {
            margin-bottom: 0;
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .alert {
            border-radius: 4px;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #FDE7E9;
            border: 1px solid #F1AEB5;
            color: #842029;
        }

        .alert-success {
            background: #D1E7DD;
            border: 1px solid #A3CFBB;
            color: #0A3622;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
            font-size: 11px;
            color: #A19F9D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invalid-feedback {
            display: block;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo-container">
            <div class="logo-badge">ERP</div>
            <div class="welcome-text">
                <h4>Welcome Back</h4>
                <p>Secure Enterprise Access Portal</p>
            </div>
        </div>

        {{-- Display success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display error message --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username"
                    class="form-control @error('username') is-invalid @enderror" 
                    placeholder="Enter your username" 
                    value="{{ old('username') }}"
                    required 
                    autofocus
                    autocomplete="username"
                >
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Enter your password" 
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-me">
                <input 
                    type="checkbox" 
                    name="remember" 
                    id="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-azure">
                Sign In
            </button>
        </form>

        <div class="footer-text">
            © {{ date('Y') }} School Management System
        </div>
    </div>
</div>

<!-- Bootstrap 5.3 JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
