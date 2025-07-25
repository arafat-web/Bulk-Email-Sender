@extends('layouts.app')

@section('title', 'Email Templates - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Email Marketing</li>
    <li class="breadcrumb-item active">Saved Templates</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-bookmark text-info me-2"></i>Saved Email Templates
            </h1>
            <p class="page-subtitle">Create, manage and reuse your email templates for instant campaigns.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-templates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Template
            </a>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if (session('success'))
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle fs-4 me-3"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-bookmark fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <div class="small">Total Templates</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ $stats['active'] }}</div>
                        <div class="small">Active Templates</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-graph-up fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_usage']) }}</div>
                        <div class="small">Total Usage</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Grid -->
<div class="row">
    @forelse($templates as $template)
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>{{ $template->name }}
                </h6>
                <div class="d-flex gap-1">
                    {!! $template->status_badge !!}
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-type me-1"></i>Subject
                    </div>
                    <div class="fw-medium">{{ \Str::limit($template->subject, 50) }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-card-text me-1"></i>Content Preview
                    </div>
                    <div class="small text-muted">{{ $template->body_preview }}</div>
                </div>

                @if($template->description)
                <div class="mb-3">
                    <div class="text-muted small mb-1">
                        <i class="bi bi-info-circle me-1"></i>Description
                    </div>
                    <div class="small">{{ $template->short_description }}</div>
                </div>
                @endif

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="fw-bold text-primary">{{ number_format($template->usage_count) }}</div>
                            <small class="text-muted">Times Used</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-info">
                            {{ $template->last_used_at ? $template->last_used_at->diffForHumans() : 'Never' }}
                        </div>
                        <small class="text-muted">Last Used</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('email-templates.show', $template) }}"
                       class="btn btn-sm btn-outline-info" title="View Template">
                        <i class="bi bi-eye me-1"></i>View
                    </a>

                    <a href="{{ route('email-templates.edit', $template) }}"
                       class="btn btn-sm btn-outline-secondary" title="Edit Template">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>

                    <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Duplicate Template">
                            <i class="bi bi-files me-1"></i>Copy
                        </button>
                    </form>

                    <form action="{{ route('email-templates.toggle-active', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-outline-{{ $template->is_active ? 'warning' : 'success' }}"
                                title="{{ $template->is_active ? 'Deactivate' : 'Activate' }} Template">
                            <i class="bi bi-{{ $template->is_active ? 'toggle-off' : 'toggle-on' }} me-1"></i>
                            {{ $template->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>

                    <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this template? This action cannot be undone.')"
                                title="Delete Template">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="bg-light rounded-circle mx-auto mb-4" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-bookmark display-3 text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">No Email Templates Found</h5>
                <p class="text-muted mb-4">Create your first email template to get started with professional email campaigns.</p>
                <a href="{{ route('email-templates.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>Create Your First Template
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
$(document).ready(function() {
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
