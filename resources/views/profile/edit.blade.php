@extends('layouts.app')

@section('title', 'Profile Settings - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile Settings</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-person-circle text-primary me-2"></i>
                Profile Settings
            </h1>
            <p class="page-subtitle">Manage your personal information and account details.</p>
        </div>
        <div class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>Account Management
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3" style="width: 48px; height: 48px; font-size: 1.2rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Personal Information</h5>
                        <small class="text-muted">Update your account details and personal information</small>
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

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Full Name
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="bg-light rounded p-3">
                                <h6 class="fw-semibold mb-2">
                                    <i class="bi bi-info-circle text-info me-1"></i>Account Information
                                </h6>
                                <div class="row text-sm">
                                    <div class="col-md-6">
                                        <small class="text-muted">Account Created:</small>
                                        <div class="fw-semibold">{{ $user->created_at ? $user->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Last Updated:</small>
                                        <div class="fw-semibold">{{ $user->updated_at ? $user->updated_at->format('M d, Y \a\t h:i A') : 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-shield-lock text-warning me-2"></i>Security Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-semibold mb-1">Password</h6>
                        <p class="text-muted mb-0 small">Keep your account secure with a strong password</p>
                    </div>
                    <a href="{{ route('settings.password') }}" class="btn btn-outline-warning">
                        <i class="bi bi-key me-1"></i>Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
