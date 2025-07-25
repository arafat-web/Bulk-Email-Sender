@extends('layouts.app')
@section('title')
    Edit Email Account - BES
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
                        <span>Edit</span>
                    </div>
                    <h2 class="az-dashboard-title">Edit Email Account</h2>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Edit: {{ $emailAccount->name }}</h5>
                            @if($emailAccount->is_default)
                                <span class="badge bg-primary">Default Account</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('email-accounts.update', $emailAccount) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name', $emailAccount->name) }}"
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
                                                   value="{{ old('email', $emailAccount->email) }}"
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
                                           value="{{ old('from_name', $emailAccount->from_name) }}"
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
                                                   value="{{ old('smtp_host', $emailAccount->smtp_host) }}"
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
                                                   value="{{ old('smtp_port', $emailAccount->smtp_port) }}"
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
                                                   value="{{ old('smtp_username', $emailAccount->smtp_username) }}"
                                                   placeholder="Usually your email address"
                                                   required>
                                            @error('smtp_username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="smtp_password" class="form-label">SMTP Password</label>
                                            <input type="password"
                                                   class="form-control @error('smtp_password') is-invalid @enderror"
                                                   id="smtp_password"
                                                   name="smtp_password"
                                                   placeholder="Leave blank to keep current password">
                                            <small class="form-text text-muted">Leave blank to keep the current password</small>
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
                                        <option value="tls" {{ old('smtp_encryption', $emailAccount->smtp_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('smtp_encryption', $emailAccount->smtp_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ old('smtp_encryption', $emailAccount->smtp_encryption) == 'none' ? 'selected' : '' }}>None</option>
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
                                              placeholder="Additional notes about this email account">{{ old('notes', $emailAccount->notes) }}</textarea>
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
                                        <i class="bi bi-check-circle me-2"></i>Update Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Statistics -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Account Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ number_format($emailAccount->emails_sent) }}</h4>
                                        <small class="text-muted">Emails Sent</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-success mb-1">{{ $emailAccount->is_active ? 'Active' : 'Inactive' }}</h4>
                                        <small class="text-muted">Status</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-info mb-1">{{ $emailAccount->created_at->diffForHumans() }}</h4>
                                        <small class="text-muted">Created</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-warning mb-1">{{ $emailAccount->last_used_at ? $emailAccount->last_used_at->diffForHumans() : 'Never' }}</h4>
                                    <small class="text-muted">Last Used</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
