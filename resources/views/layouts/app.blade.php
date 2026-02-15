<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School ERP System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --app-primary: #0078d4;
            --app-primary-dark: #005a9e;
            --app-border: #e1e4e8;
            --app-shadow: 0 1px 3px rgba(0,0,0,0.12);
            --app-shadow-lifted: 0 4px 8px rgba(0,0,0,0.15);
            --fluent-slate: #323130;
        }
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: var(--app-primary) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: white !important;
            font-weight: 600;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: white;
            border-right: 1px solid var(--app-border);
            padding: 20px 0;
        }
        .sidebar .nav-link {
            color: var(--fluent-slate);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #f0f0f0;
            border-left-color: var(--app-primary);
            color: var(--app-primary);
        }
        .sidebar .submenu {
            padding-left: 20px;
        }
        .sidebar .submenu .nav-link {
            padding: 8px 20px;
            font-size: 0.9rem;
        }
        .sidebar .nav-link[data-bs-toggle="collapse"] {
            cursor: pointer;
        }
        .sidebar .nav-link[data-bs-toggle="collapse"] .bi-chevron-down {
            transition: transform 0.2s;
        }
        .sidebar .nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .main-content {
            padding: 30px;
        }
        .card {
            border: 1px solid var(--app-border);
            box-shadow: var(--app-shadow);
            border-radius: 4px;
        }
        .btn-primary {
            background-color: var(--app-primary);
            border-color: var(--app-primary);
        }
        .btn-primary:hover {
            background-color: var(--app-primary-dark);
            border-color: var(--app-primary-dark);
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">School ERP System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">📊 Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admissions.*') || request()->routeIs('students.transfer-certificate.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#studentsSubmenu" role="button" aria-expanded="{{ request()->routeIs('admissions.*') || request()->routeIs('students.transfer-certificate.*') ? 'true' : 'false' }}" aria-controls="studentsSubmenu">
                👨‍🎓 Students <i class="bi bi-chevron-down float-end" aria-hidden="true"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admissions.*') || request()->routeIs('students.transfer-certificate.*') ? 'show' : '' }}" id="studentsSubmenu">
                <ul class="nav flex-column submenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">📋 Admissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.transfer-certificate.*') ? 'active' : '' }}" href="{{ route('students.transfer-certificate.index') }}">📄 Transfer Certificate</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('fee-packages.*') || request()->routeIs('fees.*') ? 'active' : '' }}" href="{{ route('fee-packages.index') }}">💰 Fees</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('library.*') ? 'active' : '' }}" href="{{ route('library.books.index') }}">📚 Library</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('staff.*') || request()->routeIs('departments.*') || request()->routeIs('positions.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#staffSubmenu" role="button" aria-expanded="{{ request()->routeIs('staff.*') || request()->routeIs('departments.*') || request()->routeIs('positions.*') ? 'true' : 'false' }}" aria-controls="staffSubmenu">
                👥 Staff <i class="bi bi-chevron-down float-end" aria-hidden="true"></i>
            </a>
            <div class="collapse {{ request()->routeIs('staff.*') || request()->routeIs('departments.*') || request()->routeIs('positions.*') ? 'show' : '' }}" id="staffSubmenu">
                <ul class="nav flex-column submenu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.*') && !request()->routeIs('staff-search') ? 'active' : '' }}" href="{{ route('staff.index') }}">👤 Staff Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">🏢 Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}" href="{{ route('positions.index') }}">💼 Positions</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">📝 Exams</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">📅 Attendance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.accounts.*') || request()->routeIs('categories.*') || request()->routeIs('income.*') || request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('reports.accounts.index') }}">💼 Accounts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('transport.*') ? 'active' : '' }}" href="{{ route('transport.index') }}">🚌 Transport</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">⚙️ Settings</a>
        </li>
    </ul>
</nav>
            <main class="col-md-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
