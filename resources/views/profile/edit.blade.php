@extends('layouts.app')

@section('title', 'Profile - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Profile Settings</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Manage your account details</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row justify-content-center g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Personal Information</h5></div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-top:16px;">
                        <div class="row" style="font-size:12px;">
                            <div class="col-6">
                                <span style="color:#94a3b8;">Created</span>
                                <div style="color:#0f172a;font-weight:500;">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</div>
                            </div>
                            <div class="col-6">
                                <span style="color:#94a3b8;">Last Updated</span>
                                <div style="color:#0f172a;font-weight:500;">{{ $user->updated_at ? $user->updated_at->format('M d, Y') : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title">Security</h5></div>
            <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:14px;font-weight:500;color:#0f172a;">Password</div>
                    <div style="font-size:12px;color:#94a3b8;">Keep your account secure</div>
                </div>
                <a href="{{ route('settings.password') }}" class="btn btn-outline-primary btn-sm">Change Password</a>
            </div>
        </div>
    </div>
</div>
@endsection
