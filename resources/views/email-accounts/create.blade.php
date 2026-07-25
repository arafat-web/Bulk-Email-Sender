@extends('layouts.app')

@section('title', 'Add Email Account - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-accounts.index') }}">Email Accounts</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Add Email Account</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Configure a new SMTP account for sending</p>
    </div>
    <a href="{{ route('email-accounts.index') }}" class="btn btn-outline-primary btn-sm">Back</a>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Account Configuration</h5></div>
            <div class="card-body">
                <form action="{{ route('email-accounts.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Account Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Main Marketing" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="sender@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="from_name" class="form-label">From Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('from_name') is-invalid @enderror" id="from_name" name="from_name" value="{{ old('from_name') }}" placeholder="Your Company Name" required>
                            @error('from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr style="border-color:#e2e8f0;margin:20px 0;">

                    <h6 style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:12px;">SMTP Settings</h6>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="smtp_host" class="form-label">SMTP Host <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('smtp_host') is-invalid @enderror" id="smtp_host" name="smtp_host" value="{{ old('smtp_host') }}" placeholder="smtp.gmail.com" required>
                            @error('smtp_host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="smtp_port" class="form-label">Port <span style="color:#ef4444;">*</span></label>
                            <input type="number" class="form-control @error('smtp_port') is-invalid @enderror" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', 587) }}" min="1" max="65535" required>
                            @error('smtp_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_username" class="form-label">Username <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('smtp_username') is-invalid @enderror" id="smtp_username" name="smtp_username" value="{{ old('smtp_username') }}" placeholder="Usually your email address" required>
                            @error('smtp_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_password" class="form-label">Password <span style="color:#ef4444;">*</span></label>
                            <input type="password" class="form-control @error('smtp_password') is-invalid @enderror" id="smtp_password" name="smtp_password" placeholder="Email password or app password" required>
                            @error('smtp_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_encryption" class="form-label">Encryption <span style="color:#ef4444;">*</span></label>
                            <select class="form-select @error('smtp_encryption') is-invalid @enderror" id="smtp_encryption" name="smtp_encryption" required>
                                <option value="tls" {{ old('smtp_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ old('smtp_encryption') == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                            @error('smtp_encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('email-accounts.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Quick Setup</h5></div>
            <div class="card-body">
                <p style="font-size:12px;color:#94a3b8;margin-bottom:12px;">Click a provider to pre-fill SMTP settings:</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillTemplate('smtp.gmail.com','587','tls')">
                        <span style="font-weight:600;">Gmail</span> <span style="color:#94a3b8;">&mdash; smtp.gmail.com:587 (TLS)</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillTemplate('smtp-mail.outlook.com','587','tls')">
                        <span style="font-weight:600;">Outlook</span> <span style="color:#94a3b8;">&mdash; smtp-mail.outlook.com:587 (TLS)</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillTemplate('smtp.mail.yahoo.com','587','tls')">
                        <span style="font-weight:600;">Yahoo</span> <span style="color:#94a3b8;">&mdash; smtp.mail.yahoo.com:587 (TLS)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fillTemplate(host, port, enc) {
    document.getElementById('smtp_host').value = host;
    document.getElementById('smtp_port').value = port;
    document.getElementById('smtp_encryption').value = enc;
}
</script>
@endpush
