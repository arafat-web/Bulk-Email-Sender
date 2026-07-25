<!DOCTYPE html>
<html lang="en">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdn.tiny.cloud/1/nv9zuc0lfdy6f2dqpbokjbvbqqbtsynetmcbhwwrs2c0t7no/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        :root {
            --dark: #0f172a;
            --light: #f1f5f9;
            --sidebar-width: 240px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--light);
            color: var(--dark);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--dark);
            z-index: 1020;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .sidebar-brand .brand-icon svg {
            width: 18px;
            height: 18px;
            color: #fff;
        }

        .sidebar-brand h4 {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 2px;
            letter-spacing: -0.2px;
        }

        .sidebar-brand small {
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            display: flex;
            flex-direction: column;
        }

        .nav-section {
            margin-bottom: 8px;
        }

        .nav-section-title {
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 24px 6px;
        }

        .nav-item { list-style: none; }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 0;
            transition: color 0.15s, background 0.15s;
            margin: 1px 12px;
            border-radius: 8px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .sidebar .nav-link i {
            font-size: 17px;
            width: 20px;
            text-align: center;
            opacity: 0.7;
        }

        .sidebar .nav-link.active i,
        .sidebar .nav-link:hover i { opacity: 1; }

        /* Logout */
        .logout-section {
            margin-top: auto;
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 24px;
            color: #94a3b8;
            background: none;
            border: none;
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            text-align: left;
            transition: color 0.15s;
            margin: 1px 12px;
            border-radius: 8px;
            width: calc(100% - 24px);
        }

        .logout-btn:hover {
            color: #f87171;
            background: rgba(248, 113, 113, 0.08);
        }

        .logout-btn i {
            font-size: 17px;
            width: 20px;
            text-align: center;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--light);
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            height: 60px;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1010;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-left .page-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            letter-spacing: -0.2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--dark);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .user-meta {
            font-size: 12px;
            line-height: 1.3;
        }

        .user-meta .name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-meta .role {
            color: #94a3b8;
            font-size: 11px;
        }

        .breadcrumb-wrap {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 14px;
        }

        .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
            font-size: 12px;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #94a3b8;
        }

        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }

        .breadcrumb-item a:hover { color: var(--dark); }

        .breadcrumb-item.active { color: var(--dark); font-weight: 500; }

        /* ── Page Content ── */
        .page-content {
            padding: 28px;
        }

        /* ── Cards ── */
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 12px 12px 0 0;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .card-body { padding: 20px; }

        /* ── Buttons ── */
        .btn {
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: inherit;
            transition: opacity 0.15s;
        }

        .btn:hover { opacity: 0.9; }

        .btn-primary {
            background: var(--dark);
            border-color: var(--dark);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--dark);
            border-color: var(--dark);
            opacity: 0.9;
        }

        .btn-outline-primary {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: var(--dark);
        }

        .btn-outline-primary:hover {
            background: var(--dark);
            border-color: var(--dark);
            color: #fff;
        }

        .btn-success { background: #16a34a; border-color: #16a34a; color: #fff; }
        .btn-warning { background: #d97706; border-color: #d97706; color: #fff; }
        .btn-danger  { background: #dc2626; border-color: #dc2626; color: #fff; }
        .btn-info     { background: #2563eb; border-color: #2563eb; color: #fff; }

        /* ── Forms ── */
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            padding: 8px 12px;
            font-family: inherit;
            transition: border-color 0.15s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--dark);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06);
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 4px;
        }

        /* ── Tables ── */
        .table {
            font-size: 13px;
            margin: 0;
        }

        .table th {
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px;
            background: #f8fafc;
        }

        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* ── Badges ── */
        .badge {
            font-weight: 500;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* ── Alerts ── */
        .alert {
            border-radius: 8px;
            border: none;
            font-size: 13px;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s;
            }

            .sidebar.show { transform: translateX(0); }

            .main-content { margin-left: 0; }

            .page-content { padding: 16px; }

            .topbar { padding: 0 16px; }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h4>BulkMailer</h4>
                <small>Email Marketing</small>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-grid"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('instant.campaign.*') ? 'active' : '' }}" href="{{ route('instant.campaign.create') }}">
                                <i class="bi bi-send"></i>
                                <span>Instant Campaign</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('individual-emails.*') ? 'active' : '' }}" href="{{ route('individual-emails.create') }}">
                                <i class="bi bi-envelope"></i>
                                <span>Individual Emails</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}" href="{{ route('email-templates.index') }}">
                                <i class="bi bi-file-earmark-text"></i>
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
                                <i class="bi bi-person"></i>
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

                <div class="logout-section">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-fill">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn btn-link d-lg-none p-0 text-dark" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="breadcrumb-wrap d-none d-md-block">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="user-meta d-none d-lg-block">
                            <div class="name">{{ auth()->user()->name }}</div>
                            <div class="role">Administrator</div>
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
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');

            if (sidebar.classList.contains('show')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(15, 23, 42, 0.5);
                    z-index: 1015;
                `;
                backdrop.onclick = () => {
                    sidebar.classList.remove('show');
                    backdrop.remove();
                };
                document.body.appendChild(backdrop);
            }
        }

        $(document).ready(function() {
            if ($('.data-table').length) {
                $('.data-table').DataTable({
                    responsive: true,
                    pageLength: 10,
                    processing: true,
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        paginate: {
                            next: '›',
                            previous: '‹'
                        }
                    },
                });
            }

            $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);
        });

        $(document).click(function(event) {
            if ($(window).width() < 992) {
                if (!$(event.target).closest('.sidebar, .btn, .sidebar-backdrop').length) {
                    $('#sidebar').removeClass('show');
                    $('.sidebar-backdrop').remove();
                }
            }
        });

        $(window).resize(function() {
            if ($(window).width() >= 992) {
                $('#sidebar').removeClass('show');
                $('.sidebar-backdrop').remove();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
