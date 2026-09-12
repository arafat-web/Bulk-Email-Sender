@extends('layouts.app')

@section('title', 'Queue Failures - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaign Tracker</a></li>
    <li class="breadcrumb-item active">Queue Failures</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Failed Emails (Queue)</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">
            {{ number_format($queueHealth['failed_jobs'] ?? 0) }} failed job(s) &middot;
            {{ number_format($queueHealth['pending_jobs'] ?? 0) }} pending &middot;
            These are raw <code>failed_jobs</code> rows — including sends from before the tracker existed.
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form action="{{ route('campaigns.failures.sync') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-primary btn-sm" title="Import failed_jobs into tracker campaigns"><i class="bi bi-download me-1"></i>Sync into tracker</button>
        </form>
        <form action="{{ route('campaigns.failures.retry-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Re-queue ALL {{ number_format($queueHealth['failed_jobs'] ?? 0) }} failed jobs?')">
            @csrf
            <button class="btn btn-warning btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Retry all</button>
        </form>
        <form action="{{ route('campaigns.failures.forget-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Delete ALL failed jobs? This cannot be undone.')">
            @csrf
            <button class="btn btn-outline-primary btn-sm">Clear all</button>
        </form>
        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary btn-sm">Tracker</a>
    </div>
</div>

@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(!empty($queueHealth['recent_errors']))
<div class="card mb-3" style="border-color:#fecaca;">
    <div class="card-header" style="background:#fef2f2;"><h5 class="card-title" style="color:#991b1b;">Top failure reason</h5></div>
    <div class="card-body" style="font-size:13px;">
        <code>{{ $queueHealth['recent_errors'][0]['error'] ?? '' }}</code>
        <div class="text-muted mt-1" style="font-size:12px;">Fix this and hit Retry all — most failures share one root cause (SMTP auth, throttling, DNS).</div>
    </div>
</div>
@endif

<div class="card mb-3">
    <div class="card-body" style="padding:14px 18px;">
        <form method="GET" action="{{ route('campaigns.failures') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-9">
                <label class="form-label" for="q">Search failed payloads (email / subject)</label>
                <input type="text" class="form-control" id="q" name="q" value="{{ $search }}" placeholder="e.g. gmail.com">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Search</button>
                <a href="{{ route('campaigns.failures') }}" class="btn btn-outline-primary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h5 class="card-title">Failed jobs <span class="text-muted">({{ number_format($total) }})</span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Email</th><th>Campaign</th><th>Error</th><th>Failed at</th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="text-muted">{{ $item['id'] }}</td>
                        <td style="font-weight:600;">{{ $item['email'] }}</td>
                        <td>
                            @if($item['campaign_id'])
                                <a href="{{ $item['campaign_url'] }}">#{{ $item['campaign_id'] }}</a>
                                @if($item['subject'])<div class="text-muted" style="font-size:11px;">{{ \Str::limit($item['subject'], 40) }}</div>@endif
                            @else
                                <span class="text-muted">pre-tracker</span>
                            @endif
                        </td>
                        <td style="max-width:420px;"><code style="font-size:11px;white-space:normal;word-break:break-word;">{{ \Str::limit($item['error'] ?? 'Unknown error', 220) }}</code></td>
                        <td class="text-muted">{{ $item['failed_at'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No failed jobs. 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">{{ $rows->links() }}</div>
    </div>
</div>
@endsection
