@extends('layouts.app')

@section('title', 'Email Accounts - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Accounts</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Email Accounts</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $emailAccounts->count() }} accounts &middot; {{ $emailAccounts->where('is_active', true)->count() }} active &middot; {{ number_format($emailAccounts->sum('emails_sent')) }} sent</p>
    </div>
    <a href="{{ route('email-accounts.create') }}" class="btn btn-primary btn-sm">Add Account</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($emailAccounts->count() > 0)
<div class="row g-3">
    @foreach($emailAccounts as $account)
    <div class="col-lg-6 col-xl-4">
        <div class="card">
            @if($account->is_default)
            <div class="card-header" style="background:#0f172a;color:#fff;font-size:12px;font-weight:500;padding:8px 16px;">
                <i class="bi bi-star-fill me-1"></i>Default Account
            </div>
            @endif
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">{{ $account->name }}</h6>
                    <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="row g-2" style="font-size:12px;">
                    <div class="col-12">
                        <span style="color:#94a3b8;">Email</span>
                        <div style="color:#0f172a;font-weight:500;">{{ $account->email }}</div>
                    </div>
                    <div class="col-6">
                        <span style="color:#94a3b8;">SMTP</span>
                        <div style="color:#0f172a;">{{ $account->smtp_host }}:{{ $account->smtp_port }}</div>
                    </div>
                    <div class="col-3">
                        <span style="color:#94a3b8;">Security</span>
                        <div style="color:#0f172a;">{{ strtoupper($account->smtp_encryption) }}</div>
                    </div>
                    <div class="col-3">
                        <span style="color:#94a3b8;">Sent</span>
                        <div style="color:#0f172a;font-weight:600;">{{ number_format($account->emails_sent) }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent" style="padding:12px 16px;display:flex;gap:6px;flex-wrap:wrap;">
                @if(!$account->is_default)
                <form action="{{ route('email-accounts.set-default', $account) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">Default</button>
                </form>
                @endif
                <form action="{{ route('email-accounts.test', $account) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">Test</button>
                </form>
                <a href="{{ route('email-accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('email-accounts.toggle-active', $account) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">{{ $account->is_active ? 'Disable' : 'Enable' }}</button>
                </form>
                @if(!$account->is_default || \App\Models\EmailAccount::active()->count() > 1)
                <form action="{{ route('email-accounts.destroy', $account) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this account?')">Delete</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <p style="font-size:14px;color:#94a3b8;margin-bottom:16px;">No email accounts yet.</p>
        <a href="{{ route('email-accounts.create') }}" class="btn btn-primary btn-sm">Add Your First Account</a>
    </div>
</div>
@endif
@endsection
