@extends('layouts.app')

@section('title', 'Change Password - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
    <li class="breadcrumb-item active">Password</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Change Password</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Keep your account secure</p>
    </div>
    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">Back to Profile</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row justify-content-center g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Change Password</h5></div>
            <div class="card-body">
                <div class="alert alert-info" style="font-size:13px;">
                    Password must be at least 8 characters. Use a mix of letters, numbers, and symbols.
                </div>

                <form action="{{ route('settings.password.update') }}" method="POST" id="passwordForm">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Current password">
                                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('current_password')"><i class="bi bi-eye" id="current_password_icon"></i></button>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="New password">
                                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password')"><i class="bi bi-eye" id="password_icon"></i></button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm new password">
                                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye" id="password_confirmation_icon"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(id) {
    var f = document.getElementById(id);
    var i = document.getElementById(id + '_icon');
    if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash'; }
    else { f.type = 'password'; i.className = 'bi bi-eye'; }
}
</script>
@endpush
