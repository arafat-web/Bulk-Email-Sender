@extends('layouts.app')

@section('title', 'Email Accounts - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item active">Email Accounts</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Email Account Management</h1>
            <p class="page-subtitle">Configure and manage your SMTP email accounts for bulk sending.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-accounts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Account
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle fs-4 me-3"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($emailAccounts->count() > 0)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-envelope fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ $emailAccounts->count() }}</div>
                            <div class="small">Total Accounts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ $emailAccounts->where('is_active', true)->count() }}</div>
                            <div class="small">Active Accounts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-star fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ $emailAccounts->where('is_default', true)->count() }}</div>
                            <div class="small">Default Account</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-send fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-4 fw-bold">{{ number_format($emailAccounts->sum('emails_sent')) }}</div>
                            <div class="small">Total Emails Sent</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    @forelse($emailAccounts as $account)
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100 {{ $account->is_default ? 'border-primary' : '' }}">
            @if($account->is_default)
            <div class="card-header bg-primary text-white">
                <i class="bi bi-star-fill me-2"></i>Default Account
            </div>
            @endif

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">{{ $account->name }}</h5>
                    <div class="d-flex gap-2">
                        @if($account->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-envelope me-1"></i>Email Address
                    </div>
                    <div class="fw-medium">{{ $account->email }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-person me-1"></i>From Name
                    </div>
                    <div>{{ $account->from_name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small mb-1">
                            <i class="bi bi-server me-1"></i>SMTP Host
                        </div>
                        <div class="small">{{ $account->smtp_host }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small mb-1">Port</div>
                        <div class="small">{{ $account->smtp_port }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small mb-1">Security</div>
                        <div class="small text-uppercase">{{ $account->smtp_encryption }}</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small mb-1">
                            <i class="bi bi-graph-up me-1"></i>Emails Sent
                        </div>
                        <div class="fw-medium text-primary">{{ number_format($account->emails_sent) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-1">
                            <i class="bi bi-clock me-1"></i>Last Used
                        </div>
                        <div class="small">
                            {{ $account->last_used_at ? $account->last_used_at->diffForHumans() : 'Never' }}
                        </div>
                    </div>
                </div>

                @if($account->notes)
                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-sticky me-1"></i>Notes
                    </div>
                    <div class="small">{{ Str::limit($account->notes, 100) }}</div>
                </div>
                @endif
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2 flex-wrap">
                    @if(!$account->is_default)
                    <form action="{{ route('email-accounts.set-default', $account) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                title="Set as Default" data-bs-toggle="tooltip">
                            <i class="bi bi-star me-1"></i>Default
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('email-accounts.test', $account) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-info"
                                title="Test Connection" data-bs-toggle="tooltip">
                            <i class="bi bi-envelope-check me-1"></i>Test
                        </button>
                    </form>

                    <a href="{{ route('email-accounts.edit', $account) }}"
                       class="btn btn-sm btn-outline-secondary"
                       title="Edit Account" data-bs-toggle="tooltip">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>

                    <form action="{{ route('email-accounts.toggle-active', $account) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-outline-{{ $account->is_active ? 'warning' : 'success' }}"
                                title="{{ $account->is_active ? 'Deactivate' : 'Activate' }} Account"
                                data-bs-toggle="tooltip">
                            <i class="bi bi-{{ $account->is_active ? 'toggle-off' : 'toggle-on' }} me-1"></i>
                            {{ $account->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>

                    @if(!$account->is_default || \App\Models\EmailAccount::active()->count() > 1)
                    <form action="{{ route('email-accounts.destroy', $account) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this email account? This action cannot be undone.')"
                                title="Delete Account" data-bs-toggle="tooltip">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="bg-light rounded-circle mx-auto mb-4" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-envelope-x display-3 text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">No Email Accounts Found</h5>
                <p class="text-muted mb-4">You need to add at least one email account to start sending bulk emails. Configure your SMTP settings to get started.</p>
                <a href="{{ route('email-accounts.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>Add Your First Email Account
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Animate cards on load
    $('.card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });
});
</script>
@endpush
@endsection
