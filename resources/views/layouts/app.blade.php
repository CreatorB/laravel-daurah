<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Daurah Syariyyah')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0EA5E9;
            --secondary-color: #38BDF8;
            --accent-color: #0284C7;
            --dark-accent: #0369A1;
            --text-dark: #0f172a;
            --text-light: #64748b;
            --bg-light: #f0f9ff;
            --gradient-start: #0EA5E9;
            --gradient-end: #38BDF8;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        * {
            border-radius: 12px !important;
        }
        
        body {
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #e0f2fe 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)) !important;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);
            padding: 0 !important;
            position: sticky;
            top: 0;
            z-index: 1020;
            border-radius: 0 !important;
            margin: 0 !important;
        }
        
        .navbar .container {
            padding: 0.75rem 15px !important;
            border-radius: 0 !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 1.1rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-color), var(--gradient-start));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(14, 165, 233, 0.15);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 20px 20px 0 0 !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
            padding: 1rem 1.25rem;
            font-size: 1rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0f2fe;
            border-radius: 12px;
            transition: all 0.3s ease;
            padding: 0.625rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
        }
        
        .alert {
            border: none;
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #34d399, #10b981);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f87171, #ef4444);
        }
        
        .alert-info {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }
        
        .badge {
            border-radius: 8px !important;
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
        }
        
        .badge.bg-primary, .badge.bg-success, .badge.bg-warning, .badge.bg-info, .badge.bg-secondary {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)) !important;
            border: none;
        }
        
        .table {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }
        
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(14, 165, 233, 0.3);
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
            100% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
        }
        
        .btn-hadir {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px !important;
            background: linear-gradient(135deg, #10b981, #34d399);
            border: none;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-hadir:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }
        
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e0f2fe;
            transition: all 0.3s ease;
            padding: 1rem 1.25rem;
        }
        
        .list-group-item:hover {
            background: #f0f9ff;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border: none;
            border-radius: 12px 0 0 12px !important;
            padding: 0.625rem 1rem;
        }
        
        .form-check-input:checked {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }
        
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        main.py-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        
        /* Mobile First - Base styles for small screens */
        .container {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        h3 {
            font-size: 1.25rem;
        }
        
        h4 {
            font-size: 1.125rem;
        }
        
        .btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.9rem;
        }
        
        .btn i {
            font-size: 0.9rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Dashboard stats cards - mobile first */
        .row.g-4 .col-6 {
            padding: 0.5rem;
        }
        
        .row.g-4 .col-6 .card {
            padding: 1rem 0.5rem;
        }
        
        /* Table responsive improvements */
        .table-responsive {
            border-radius: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--gradient-start);
            border-radius: 10px;
        }
        
        /* Navbar mobile */
        .navbar-collapse {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border-radius: 12px;
            margin-top: 0.5rem;
            padding: 1rem;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem 0.75rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
        }
        
        /* Mobile specific adjustments */
        @media (max-width: 575.98px) {
            .container {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            h2 {
                font-size: 1.25rem;
            }
            
            h3 {
                font-size: 1.125rem;
            }
            
            h4 {
                font-size: 1rem;
            }
            
            .card {
                border-radius: 16px;
            }
            
            .card-header {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .btn-sm, .btn-group-sm > .btn {
                padding: 0.35rem 0.5rem;
                font-size: 0.8rem;
                width: auto;
                margin-bottom: 0;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
            
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            
            .table {
                font-size: 0.75rem;
            }
            
            .table thead th {
                padding: 0.5rem 0.75rem;
            }
            
            .table tbody td {
                padding: 0.5rem 0.75rem;
            }
            
            .btn-hadir {
                padding: 12px 24px;
                font-size: 1rem;
            }
            
            .list-group-item {
                padding: 0.875rem 1rem;
            }
            
            .form-control, .form-select {
                padding: 0.5rem 0.875rem;
                font-size: 0.9rem;
            }
            
            .input-group-text {
                padding: 0.5rem 0.875rem;
                font-size: 0.9rem;
            }
            
            .badge {
                font-size: 0.7rem;
                padding: 0.25em 0.5em;
            }
            
            .nav-link {
                font-size: 0.85rem;
                padding: 0.4rem 0.75rem !important;
            }
            
            .alert {
                padding: 0.75rem 1rem;
                font-size: 0.85rem;
            }
            
            .row.g-4 .col-6 .card {
                padding: 0.75rem 0.25rem;
            }
            
            .col-6.col-md-3 .card .fa-2x {
                font-size: 1.5rem;
            }
            
            .col-6.col-md-3 .card h3 {
                font-size: 1.25rem;
            }
            
            .col-6.col-md-3 .card small {
                font-size: 0.7rem;
            }
            
            main.py-4 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.75rem;
            }
            
            .d-flex.justify-content-between.align-items-center .btn {
                width: 100%;
            }
        }
        
        /* Tablet adjustments */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .container {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
            
            .btn {
                padding: 0.5625rem 1.125rem;
                font-size: 0.875rem;
            }
            
            .card-body {
                padding: 1.125rem;
            }
            
            .table {
                font-size: 0.8rem;
            }
        }
        
        /* Small desktop */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .container {
                max-width: 100%;
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }
        
        /* Medium desktop and up */
        @media (min-width: 992px) {
            .container {
                max-width: 1140px;
            }
            
            .btn {
                white-space: nowrap;
            }
        }
        
        /* Large desktop */
        @media (min-width: 1200px) {
            .container {
                max-width: 1320px;
            }
        }
        
        /* Utility classes for mobile */
        .stack-on-mobile {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
        }
        
        @media (max-width: 575.98px) {
            .stack-on-mobile {
                flex-direction: column;
            }
            
            .stack-on-mobile .btn {
                width: 100%;
            }
            
            .hide-on-mobile {
                display: none !important;
            }
            
            .text-md-end {
                text-align: center !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-mosque me-2"></i>{{ config('app.daurah_name') }}
            </a>
            @if(session('user_id'))
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if(session('role') === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.events.index') }}">
                            <i class="fas fa-calendar me-1"></i>Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.konfirmasi.index') }}">
                            <i class="fas fa-check-circle me-1"></i>Konfirmasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.profile') }}">
                            <i class="fas fa-user me-1"></i>Profil
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
