<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bulk Email Sender - Professional Email Marketing Solution">
    <meta name="author" content="Arafat Hossain">
    <title>@yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdn.tiny.cloud/1/nv9zuc0lfdy6f2dqpbokjbvbqqbtsynetmcbhwwrs2c0t7no/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        :root {
            --primary-color: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #0f172a;
            --light-color: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-width: 280px;
            --topbar-height: 80px;
            --border-radius: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--gray-800);
            font-size: 14px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Modern Sidebar Design */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #3730a3 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(139, 92, 246, 0.1);
            z-index: 1040;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(139, 92, 246, 0.1) 0%, rgba(124, 58, 237, 0.05) 100%);
            pointer-events: none;
        }

        .sidebar-brand {
            position: relative;
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: var(--shadow-lg);
        }

        .sidebar-brand .brand-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
            letter-spacing: -0.025em;
        }

        .sidebar-brand small {
            color: #a78bfa;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Modern Navigation Styles */
        .sidebar-nav {
            padding: 1.5rem 0;
            position: relative;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: #a78bfa;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 1.5rem 0.75rem;
            margin-bottom: 0.5rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            transform-origin: bottom;
        }

        .nav-link:hover {
            background: rgba(139, 92, 246, 0.1);
            color: white;
            transform: translateX(8px);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        .nav-link.active {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2) 0%, rgba(124, 58, 237, 0.1) 100%);
            color: white;
            transform: translateX(8px);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 1rem;
            width: 6px;
            height: 6px;
            background: var(--primary-color);
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .nav-link i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.875rem;
            font-size: 1.125rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover i,
        .nav-link.active i {
            background: rgba(139, 92, 246, 0.2);
            color: var(--primary-color);
        }

        /* Logout Button Special Styling */
        .logout-nav-item {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #f87171;
            text-decoration: none;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .logout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            transform-origin: bottom;
        }

        .logout-btn:hover {
            background: rgba(248, 113, 113, 0.1);
            color: #fca5a5;
            transform: translateX(8px);
        }

        .logout-btn:hover::before {
            transform: scaleY(1);
        }

        .logout-btn i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.875rem;
            font-size: 1.125rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #f87171;
        }

        .logout-btn:hover i {
            background: rgba(248, 113, 113, 0.2);
            color: #fca5a5;
            transform: rotate(10deg);
        }

        /* Modern Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }

        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e2e8f0' fill-opacity='0.3'%3E%3Ccircle cx='7' cy='7' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
            opacity: 0.5;
        }

        /* Modern Topbar */
        .topbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            height: var(--topbar-height);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
            z-index: 1030;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin: 0;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Modern Breadcrumb */
        .modern-breadcrumb {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: var(--gray-400);
            font-weight: 600;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-dark);
        }

        .breadcrumb-item.active {
            color: var(--gray-600);
            font-weight: 600;
        }

        .breadcrumb-item.active {
            color: var(--gray-600);
            font-weight: 600;
        }

        /* Modern Content Area */
        .page-content {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .page-header {
            margin-bottom: 2.5rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            padding: 2rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--shadow-sm);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--primary-color) 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        /* Modern Cards */
        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
            border-color: rgba(139, 92, 246, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(6, 182, 212, 0.02) 100%);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 1.5rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
            letter-spacing: -0.025em;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Modern Stats Cards */
        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            padding: 2rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--color);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--color);
        }

        .stats-card .stats-label {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        /* Modern User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        /* Modern Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            border: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: var(--shadow-sm);
            letter-spacing: 0.025em;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #6d28d9 100%);
            color: white;
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
            color: white;
        }

        /* Modern Forms */
        .form-control {
            border-radius: 12px;
            border: 2px solid var(--gray-200);
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.15);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        /* Modern Tables */
        .table {
            font-size: 0.875rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table th {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            font-weight: 700;
            color: var(--gray-800);
            border-bottom: 2px solid var(--gray-200);
            padding: 1rem 0.75rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .page-content {
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stats-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .topbar {
                padding: 0 1rem;
            }

            .page-content {
                padding: 0.75rem;
            }

            .card-body {
                padding: 1rem;
            }

            .stats-card {
                padding: 1rem;
            }

            .stats-card .stats-number {
                font-size: 1.5rem;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            [data-bs-theme="auto"] {
                --gray-50: #111827;
                --gray-100: #1f2937;
                --gray-200: #374151;
                --gray-300: #4b5563;
                --gray-400: #6b7280;
                --gray-500: #9ca3af;
                --gray-600: #d1d5db;
                --gray-700: #e5e7eb;
                --gray-800: #f3f4f6;
                --gray-900: #f9fafb;
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--gray-200);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Additional Modern Components */
        .hover-shadow {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }

        .progress {
            border-radius: 8px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 8px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        .table td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            font-size: 0.875rem;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .page-content {
                padding: 1rem;
            }
        }

        /* User Profile Dropdown */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--bs-primary) 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Modern Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-envelope-paper"></i>
                </div>
                <h4>BulkMailer</h4>
                <small>Professional Email Marketing</small>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main Menu</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-grid-1x2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('instant.campaign.*') ? 'active' : '' }}" href="{{ route('instant.campaign.create') }}">
                                <i class="bi bi-rocket-takeoff"></i>
                                <span>Instant Campaign</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}" href="{{ route('email-templates.index') }}">
                                <i class="bi bi-file-earmark-richtext"></i>
                                <span>Email Templates</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-accounts.*') ? 'active' : '' }}" href="{{ route('email-accounts.index') }}">
                                <i class="bi bi-envelope-check"></i>
                                <span>Email Accounts</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle"></i>
                                <span>Profile</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.password') }}">
                                <i class="bi bi-shield-lock"></i>
                                <span>Security</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Logout Section -->
                <div class="nav-section">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item logout-nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Modern Main Content -->
        <div class="main-content flex-fill">
            <!-- Modern Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn btn-link d-lg-none p-0" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-4 text-gray-600"></i>
                    </button>
                    <div class="page-title-wrapper">
                        <div class="modern-breadcrumb d-none d-md-block">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="d-flex align-items-center bg-light rounded-pill px-4 py-2">
                        <div class="user-avatar me-3">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="d-none d-lg-block">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Welcome back</small>
                            <div class="fw-semibold text-dark" style="font-size: 0.875rem;">{{ auth()->user()->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

    <script>
        // Modern Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');

            // Add backdrop for mobile
            if (sidebar.classList.contains('show')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    z-index: 1040;
                    backdrop-filter: blur(4px);
                `;
                backdrop.onclick = () => {
                    sidebar.classList.remove('show');
                    backdrop.remove();
                };
                document.body.appendChild(backdrop);
            }
        }

        // Enhanced DataTables initialization
        $(document).ready(function() {
            // Modern loading animation
            if ($('.data-table').length) {
                $('.data-table').DataTable({
                    responsive: true,
                    pageLength: 10,
                    processing: true,
                    language: {
                        search: '<i class="bi bi-search me-2"></i>Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'No entries available',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        paginate: {
                            first: '<i class="bi bi-chevron-double-left"></i>',
                            last: '<i class="bi bi-chevron-double-right"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>'
                        },
                        processing: '<div class="d-flex align-items-center"><div class="loading-spinner me-3"></div>Loading...</div>'
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                });
            }

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Auto-hide alerts after 5 seconds
            $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);

            // Add loading states to buttons
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                if (submitBtn.length) {
                    submitBtn.prop('disabled', true);
                    const originalText = submitBtn.html();
                    submitBtn.html('<div class="loading-spinner me-2" style="width: 16px; height: 16px;"></div>Processing...');

                    setTimeout(() => {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }, 10000);
                }
            });
        });

        // Close sidebar when clicking outside on mobile
        $(document).click(function(event) {
            if ($(window).width() < 992) {
                if (!$(event.target).closest('.sidebar, .btn, .sidebar-backdrop').length) {
                    $('#sidebar').removeClass('show');
                    $('.sidebar-backdrop').remove();
                }
            }
        });

        // Window resize handler
        $(window).resize(function() {
            if ($(window).width() >= 992) {
                $('#sidebar').removeClass('show');
                $('.sidebar-backdrop').remove();
            }
        });

        // Page transition effect
        window.addEventListener('beforeunload', function() {
            document.body.style.opacity = '0.5';
        });

        // Theme toggle (if needed in future)
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Initialize theme from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        });
    </script>

    @stack('scripts')
</body>
</html>
