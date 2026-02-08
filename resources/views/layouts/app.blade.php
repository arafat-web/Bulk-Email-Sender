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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">


    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Modern Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <h4>BulkMailer</h4>
                <small>Email Marketing</small>
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
                            <a class="nav-link {{ request()->routeIs('individual-emails.*') ? 'active' : '' }}" href="{{ route('individual-emails.create') }}">
                                <i class="bi bi-envelope-heart"></i>
                                <span>Individual Emails</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}" href="{{ route('email-templates.index') }}">
                                <i class="bi bi-file-earmark-richtext"></i>
                                <span>Email Templates</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Email Contacts</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}" href="{{ route('tags.index') }}">
                                <i class="bi bi-tags"></i>
                                <span>Contact Tags</span>
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
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
