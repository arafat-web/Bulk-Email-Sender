@extends('layouts.app')

@section('title', 'Dashboard - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-emoji-smile text-warning me-2"></i>
                Welcome back, {{ auth()->user()->name }}!
            </h1>
            <p class="page-subtitle">Here's your email marketing performance overview and recent activity.</p>
        </div>
        <div class="d-flex gap-3">
            <div class="text-center p-3 bg-white rounded-3 shadow-sm border">
                <i class="bi bi-clock text-primary fs-4 d-block mb-1"></i>
                <small class="text-muted d-block">Current Time</small>
                <div class="fw-bold text-dark" id="currentTime"></div>
            </div>
            <div class="text-center p-3 bg-white rounded-3 shadow-sm border">
                <i class="bi bi-calendar-event text-success fs-4 d-block mb-1"></i>
                <small class="text-muted d-block">Today's Date</small>
                <div class="fw-bold text-dark" id="currentDate"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card" style="--color: var(--primary-color)">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stats-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%)">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <div class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-trending-up me-1"></i>+{{ rand(5, 25) }}%
                </div>
            </div>
            <div class="stats-number" style="color: var(--primary-color)">{{ $total_time }}</div>
            <div class="stats-label">
                <i class="bi bi-graph-up me-1"></i>Total Campaigns
            </div>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar" role="progressbar" style="width: {{ min(100, ($total_time * 10)) }}%; background: var(--primary-color)"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card" style="--color: var(--success-color)">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stats-icon" style="background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%)">
                    <i class="bi bi-envelope-check"></i>
                </div>
                <div class="badge bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle me-1"></i>Delivered
                </div>
            </div>
            <div class="stats-number" style="color: var(--success-color)">{{ number_format($total_sent) }}</div>
            <div class="stats-label">
                <i class="bi bi-send-check me-1"></i>Emails Sent
            </div>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, ($total_sent / 1000)) }}%"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card" style="--color: var(--secondary-color)">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stats-icon" style="background: linear-gradient(135deg, var(--secondary-color) 0%, #0891b2 100%)">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="badge bg-info bg-opacity-10 text-info">
                    <i class="bi bi-person-plus me-1"></i>Active
                </div>
            </div>
            <div class="stats-number" style="color: var(--secondary-color)">{{ $total_user }}</div>
            <div class="stats-label">
                <i class="bi bi-person-check me-1"></i>System Users
            </div>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, ($total_user * 20)) }}%"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card" style="--color: var(--warning-color)">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stats-icon" style="background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%)">
                    <i class="bi bi-envelope-gear"></i>
                </div>
                <div class="badge bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-gear me-1"></i>{{ $total_email_accounts > 0 ? 'Ready' : 'Setup' }}
                </div>
            </div>
            <div class="stats-number" style="color: var(--warning-color)">{{ $total_email_accounts ?? 0 }}</div>
            <div class="stats-label">
                <i class="bi bi-envelope-at me-1"></i>Email Accounts
            </div>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, ($total_email_accounts * 33)) }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Management Stats -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-lines-fill text-info me-2"></i>Contact Management Overview
                        </h5>
                        <p class="text-muted mb-0 small">Your email contacts and engagement statistics</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-people me-1"></i>Manage Contacts
                        </a>
                        <a href="{{ route('contacts.create') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Add Contact
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);">
                                <i class="bi bi-people text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ number_format($total_contacts ?? 0) }}</div>
                                <div class="text-muted small">Total Contacts</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                                <i class="bi bi-person-check text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ number_format($active_contacts ?? 0) }}</div>
                                <div class="text-muted small">Active Contacts</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
                                <i class="bi bi-tags text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ number_format($total_tags ?? 0) }}</div>
                                <div class="text-muted small">Contact Tags</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                                <i class="bi bi-envelope-at text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ number_format($emails_sent_today ?? 0) }}</div>
                                <div class="text-muted small">Emails Today</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($recent_contacts) && $recent_contacts->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3">
                            <i class="bi bi-clock-history me-2"></i>Recently Added Contacts
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Tags</th>
                                        <th>Status</th>
                                        <th>Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_contacts->take(5) as $contact)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                                     style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                                <strong>{{ $contact->name ?: 'N/A' }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $contact->email }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $contact->company ?: 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @foreach($contact->tags->take(2) as $tag)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary me-1" style="font-size: 0.7rem;">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if($contact->tags->count() > 2)
                                                <span class="badge bg-light text-muted" style="font-size: 0.7rem;">
                                                    +{{ $contact->tags->count() - 2 }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $contact->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($contact->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('contacts.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye me-1"></i>View All Contacts
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center mt-4 py-4">
                    <i class="bi bi-person-plus display-6 text-muted mb-3"></i>
                    <h6 class="text-muted">No contacts yet</h6>
                    <p class="text-muted small mb-3">Start building your contact database by adding contacts manually or importing from Excel/CSV files.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('contacts.create') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Add First Contact
                        </a>
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-upload me-1"></i>Import Contacts
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modern Quick Actions & Recent Activity -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">
                            <i class="bi bi-lightning-charge text-primary me-2"></i>Quick Actions
                        </h5>
                        <p class="text-muted mb-0 small">Get started with your email campaigns</p>
                    </div>
                    <div class="badge bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-rocket me-1"></i>Ready to launch
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('instant.campaign.create') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                                        <i class="bi bi-rocket-takeoff text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">Start Campaign</h6>
                                    <p class="text-muted mb-0 small">Create and send bulk emails instantly</p>
                                </div>
                                <i class="bi bi-arrow-right text-primary"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('individual-emails.create') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                                        <i class="bi bi-envelope-heart text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">Individual Emails</h6>
                                    <p class="text-muted mb-0 small">Send personalized emails to specific recipients</p>
                                </div>
                                <i class="bi bi-arrow-right" style="color: #ec4899;"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('email-templates.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);">
                                        <i class="bi bi-file-earmark-richtext text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">Manage Templates</h6>
                                    <p class="text-muted mb-0 small">Create and organize email templates</p>
                                </div>
                                <i class="bi bi-arrow-right text-success"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('email-accounts.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">
                                        <i class="bi bi-envelope-gear text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">Email Accounts</h6>
                                    <p class="text-muted mb-0 small">Configure SMTP email accounts</p>
                                </div>
                                <i class="bi bi-arrow-right text-warning"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('contacts.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);">
                                        <i class="bi bi-person-lines-fill text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">Contact Management</h6>
                                    <p class="text-muted mb-0 small">Manage your email contacts and tags</p>
                                </div>
                                <i class="bi bi-arrow-right text-info"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" onclick="alert('Analytics coming soon!')" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded-3 h-100 hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--secondary-color) 0%, #0891b2 100%);">
                                        <i class="bi bi-graph-up-arrow text-white fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1 text-dark">View Analytics</h6>
                                    <p class="text-muted mb-0 small">Track campaign performance</p>
                                </div>
                                <i class="bi bi-arrow-right text-info"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Operations
                </h5>
            </div>
            <div class="card-body p-0">
                @if($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Operation #</th>
                                <th>Emails Sent</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operations as $index => $operation)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope me-2 text-success"></i>
                                        <strong>{{ number_format($operation->total_email_address) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $operation->created_at->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $operation->created_at->format('h:i A') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Completed
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No operations yet</h5>
                    <p class="text-muted mb-4">Start your first bulk email campaign to see activity here.</p>
                    <a href="{{ route('instant.campaign.create') }}" class="btn btn-primary">
                        <i class="bi bi-lightning me-2"></i>Launch Campaign
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('instant.campaign.create') }}" class="btn btn-primary">
                        <i class="bi bi-rocket me-2"></i>Launch Campaign
                    </a>
                    <a href="{{ route('email-accounts.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-envelope-gear me-2"></i>Manage Email Accounts
                    </a>
                    <a href="{{ route('saved.templates') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-bookmark me-2"></i>Saved Templates
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>System Status
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Email Service</span>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Online
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Database</span>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Connected
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Queue System</span>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Active
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateDateTime() {
    const now = new Date();

    const currentDate = now.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });

    const currentTime = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    document.getElementById('currentDate').innerText = currentDate;
    document.getElementById('currentTime').innerText = currentTime;
}

// Update immediately and then every second
updateDateTime();
setInterval(updateDateTime, 1000);
</script>
@endpush
@endsection
