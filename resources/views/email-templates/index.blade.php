@extends('layouts.app')

@section('title', 'Email Templates - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Templates</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Email Templates</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $stats['total'] }} templates &middot; {{ $stats['active'] }} active &middot; {{ number_format($stats['total_usage']) }} uses</p>
    </div>
    <a href="{{ route('email-templates.create') }}" class="btn btn-primary btn-sm">Create Template</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    @forelse($templates as $template)
    <div class="col-lg-6 col-xl-4">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">{{ $template->name }}</h6>
                    <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div style="font-size:12px;color:#64748b;margin-bottom:2px;">Subject</div>
                <div style="font-size:13px;color:#0f172a;margin-bottom:12px;">{{ Str::limit($template->subject, 55) }}</div>
                <div style="font-size:12px;color:#94a3b8;line-height:1.5;margin-bottom:14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $template->body_preview }}</div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('email-templates.show', $template) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary">Copy</button>
                    </form>
                    <form action="{{ route('email-templates.toggle-active', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary">{{ $template->is_active ? 'Disable' : 'Enable' }}</button>
                    </form>
                    <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this template?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <p style="font-size:14px;color:#94a3b8;margin-bottom:16px;">No templates yet.</p>
                <a href="{{ route('email-templates.create') }}" class="btn btn-primary">Create Your First Template</a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
