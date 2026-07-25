@extends('layouts.app')

@section('title', 'Dashboard - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Welcome back, {{ auth()->user()->name }}</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ date('l, F j, Y') }}</p>
    </div>
    <div class="card" style="padding:12px 18px;display:flex;align-items:center;gap:10px;flex-direction:row;white-space:nowrap;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span id="liveTime" style="font-size:17px;font-weight:600;color:#0f172a;font-variant-numeric:tabular-nums;"></span>
    </div>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-bottom:8px;">Total Campaigns</div>
                <div style="font-size:28px;font-weight:600;color:#0f172a;">{{ $total_time }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-bottom:8px;">Emails Sent</div>
                <div style="font-size:28px;font-weight:600;color:#0f172a;">{{ number_format($total_sent) }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-bottom:8px;">System Users</div>
                <div style="font-size:28px;font-weight:600;color:#0f172a;">{{ $total_user }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-bottom:8px;">Email Accounts</div>
                <div style="font-size:28px;font-weight:600;color:#0f172a;">{{ $total_email_accounts ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Contacts stats -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;">Total Contacts</div>
                <div style="font-size:22px;font-weight:600;color:#0f172a;margin-top:4px;">{{ number_format($total_contacts ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;">Active Contacts</div>
                <div style="font-size:22px;font-weight:600;color:#0f172a;margin-top:4px;">{{ number_format($active_contacts ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;">Contact Tags</div>
                <div style="font-size:22px;font-weight:600;color:#0f172a;margin-top:4px;">{{ number_format($total_tags ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:12px;color:#64748b;font-weight:500;">Emails Today</div>
                <div style="font-size:22px;font-weight:600;color:#0f172a;margin-top:4px;">{{ number_format($emails_sent_today ?? 0) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick actions + Recent operations -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <a href="{{ route('instant.campaign.create') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Start Campaign</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Send bulk emails</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('individual-emails.create') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Individual Email</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Personalized messages</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('email-templates.index') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Templates</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Manage templates</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('email-accounts.index') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Email Accounts</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">SMTP settings</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('contacts.index') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Contacts</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Manage contacts</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('tags.index') }}" class="text-decoration-none">
                            <div class="qa-tile">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">Tags</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Organize contacts</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Campaigns</h5>
            </div>
            <div class="card-body p-0">
                @if($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sent</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operations->take(8) as $index => $operation)
                            <tr>
                                <td><span class="badge bg-dark">{{ $index + 1 }}</span></td>
                                <td><strong>{{ number_format($operation->total_email_address) }}</strong></td>
                                <td><span class="text-muted">{{ $operation->created_at->format('M d, Y') }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted mb-0" style="font-size:13px;">No campaigns yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent contacts -->
@if(isset($recent_contacts) && $recent_contacts->count() > 0)
<div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
        <h5 class="card-title">Recently Added Contacts</h5>
        <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary btn-sm">View all</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                        <td><strong>{{ $contact->name ?: 'N/A' }}</strong></td>
                        <td><span class="text-muted">{{ $contact->email }}</span></td>
                        <td><span class="text-muted">{{ $contact->company ?: '—' }}</span></td>
                        <td>
                            @foreach($contact->tags->take(2) as $tag)
                                <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                            @endforeach
                            @if($contact->tags->count() > 2)
                                <span class="badge bg-light text-muted">+{{ $contact->tags->count() - 2 }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $contact->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </td>
                        <td><span class="text-muted">{{ $contact->created_at->diffForHumans() }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
.qa-tile {
    padding: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
}
.qa-tile:hover {
    border-color: #0f172a;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transform: translateY(-2px);
}
@media (max-width: 575.98px) {
    .qa-tile {
        padding: 12px;
    }
    .qa-tile div:first-child {
        font-size: 12px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function tick() {
    const el = document.getElementById('liveTime');
    if (el) {
        el.innerText = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    requestAnimationFrame(() => setTimeout(tick, 1000));
})();
</script>
@endpush
@endsection
