@extends('layouts.app')
@section('title')
    Add Email Account - BES
@endsection

@section('content')
<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <div class="az-content-breadcrumb">
                        <span>Settings</span>
                        <span><a href="{{ route('email-accounts.index') }}">Email Accounts</a></span>
                        <span>Add New</span>
                    </div>
                    <h2 class="az-dashboard-title">Add New Email Account</h2>
                </div>
                <div class="az-content-header-right">
                    <a href="{{ route('email-accounts.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Email Account Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('email-accounts.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name') }}"
                                                   placeholder="e.g., Main Marketing Account"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email') }}"
                                                   placeholder="sender@example.com"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('from_name') is-invalid @enderror"
                                           id="from_name"
                                           name="from_name"
                                           value="{{ old('from_name') }}"
                                           placeholder="Your Company Name"
                                           required>
                                    @error('from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">SMTP Configuration</h6>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('smtp_host') is-invalid @enderror"
                                                   id="smtp_host"
                                                   name="smtp_host"
                                                   value="{{ old('smtp_host') }}"
                                                   placeholder="smtp.gmail.com"
                                                   required>
                                            @error('smtp_host')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="smtp_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   class="form-control @error('smtp_port') is-invalid @enderror"
                                                   id="smtp_port"
                                                   name="smtp_port"
                                                   value="{{ old('smtp_port', 587) }}"
                                                   min="1"
                                                   max="65535"
                                                   required>
                                            @error('smtp_port')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="smtp_username" class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('smtp_username') is-invalid @enderror"
                                                   id="smtp_username"
                                                   name="smtp_username"
                                                   value="{{ old('smtp_username') }}"
                                                   placeholder="Usually your email address"
                                                   required>
                                            @error('smtp_username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="smtp_password" class="form-label">SMTP Password <span class="text-danger">*</span></label>
                                            <input type="password"
                                                   class="form-control @error('smtp_password') is-invalid @enderror"
                                                   id="smtp_password"
                                                   name="smtp_password"
                                                   placeholder="Your email password or app password"
                                                   required>
                                            @error('smtp_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="smtp_encryption" class="form-label">Encryption <span class="text-danger">*</span></label>
                                    <select class="form-select @error('smtp_encryption') is-invalid @enderror"
                                            id="smtp_encryption"
                                            name="smtp_encryption"
                                            required>
                                        <option value="tls" {{ old('smtp_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ old('smtp_encryption') == 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                    @error('smtp_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes"
                                              name="notes"
                                              rows="3"
                                              placeholder="Additional notes about this email account">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('email-accounts.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Setup Templates -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Quick Setup Templates</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>Gmail</h6>
                                        <small class="text-muted">smtp.gmail.com:587 (TLS)</small>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 d-block w-100"
                                                onclick="fillGmail()">Use Template</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>Outlook</h6>
                                        <small class="text-muted">smtp-mail.outlook.com:587 (TLS)</small>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 d-block w-100"
                                                onclick="fillOutlook()">Use Template</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>Yahoo</h6>
                                        <small class="text-muted">smtp.mail.yahoo.com:587 (TLS)</small>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 d-block w-100"
                                                onclick="fillYahoo()">Use Template</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillGmail() {
    document.getElementById('smtp_host').value = 'smtp.gmail.com';
    document.getElementById('smtp_port').value = '587';
    document.getElementById('smtp_encryption').value = 'tls';
}

function fillOutlook() {
    document.getElementById('smtp_host').value = 'smtp-mail.outlook.com';
    document.getElementById('smtp_port').value = '587';
    document.getElementById('smtp_encryption').value = 'tls';
}

function fillYahoo() {
    document.getElementById('smtp_host').value = 'smtp.mail.yahoo.com';
    document.getElementById('smtp_port').value = '587';
    document.getElementById('smtp_encryption').value = 'tls';
}
</script>
@endsection
