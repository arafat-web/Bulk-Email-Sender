@extends('layouts.app')

@section('title', 'Security Settings - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
    <li class="breadcrumb-item active">Security Settings</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-shield-lock text-warning me-2"></i>
                Security Settings
            </h1>
            <p class="page-subtitle">Update your password to keep your account secure.</p>
        </div>
        <div class="badge bg-warning bg-opacity-10 text-warning fs-6 px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>Account Security
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">
                        <i class="bi bi-key text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Change Password</h5>
                        <small class="text-muted">Enter your current password and choose a new secure password</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Password Requirements Info -->
                <div class="alert alert-info border-0" style="background: rgba(13, 202, 240, 0.1);">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle me-1"></i>Password Requirements
                    </h6>
                    <ul class="mb-0 small">
                        <li>At least 8 characters long</li>
                        <li>Must be confirmed by typing it twice</li>
                        <li>Consider using a mix of letters, numbers, and symbols</li>
                    </ul>
                </div>

                <form action="{{ route('settings.password.update') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-12">
                            <label for="current_password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Current Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password"
                                       name="current_password"
                                       required
                                       placeholder="Enter your current password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-shield-plus me-1"></i>New Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required
                                       placeholder="Enter new password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="password_icon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="bi bi-shield-check me-1"></i>Confirm New Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required
                                       placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Profile
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-check me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb text-info me-2"></i>Security Tips
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Use Strong Passwords</small>
                                <p class="text-muted mb-0 small">Combine letters, numbers, and symbols for better security.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Regular Updates</small>
                                <p class="text-muted mb-0 small">Change your password regularly to maintain security.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Avoid Common Words</small>
                                <p class="text-muted mb-0 small">Don't use easily guessable information like names or dates.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Unique Passwords</small>
                                <p class="text-muted mb-0 small">Use different passwords for different accounts.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;

    if (password !== confirmation) {
        e.preventDefault();
        alert('New password and confirmation do not match!');
        return false;
    }

    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        return false;
    }
});
</script>
@endpush
@endsection
