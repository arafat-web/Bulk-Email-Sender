@extends('layouts.app')

@section('title', $template->name . ' - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Saved Templates</a></li>
    <li class="breadcrumb-item active">{{ $template->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center mb-2">
                <h1 class="page-title me-3">
                    <i class="bi bi-file-earmark-text text-info me-2"></i>{{ $template->name }}
                </h1>
                {!! $template->getStatusBadge() !!}
            </div>
            <p class="page-subtitle">
                {{ $template->description ?: 'Email template for marketing campaigns' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-2"></i>Edit Template
            </a>
            <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Templates
            </a>
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <!-- Template Preview -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>Template Preview
                </h5>
            </div>
            <div class="card-body">
                <!-- Email Header -->
                <div class="email-preview border rounded p-4" style="background: #f8f9fa;">
                    <div class="email-header mb-3 pb-3 border-bottom">
                        <div class="row">
                            <div class="col-sm-2 text-muted small fw-bold">From:</div>
                            <div class="col-sm-10 small">Your Campaign Email</div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-2 text-muted small fw-bold">Subject:</div>
                            <div class="col-sm-10 small fw-bold">{{ $template->subject }}</div>
                        </div>
                    </div>

                    <!-- Email Body -->
                    <div class="email-body">
                        {!! $template->body !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>Template Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-warning w-100">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Template
                        </a>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="d-inline w-100" id="duplicateForm">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-files me-2"></i>
                                Duplicate Template
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('email-templates.toggle', $template) }}" method="POST" class="d-inline w-100" id="toggleForm">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $template->is_active ? 'warning' : 'success' }} w-100">
                                <i class="bi bi-{{ $template->is_active ? 'pause' : 'play' }}-circle me-2"></i>
                                {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Use Template Button -->
                <div class="text-center">
                    <a href="{{ route('instant.campaign.create') }}?template={{ $template->id }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-send me-2"></i>
                        Use This Template in Campaign
                    </a>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        This will pre-fill the campaign form with this template's content.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Template Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Template Information
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>{!! $template->getStatusBadge() !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created:</td>
                        <td>{{ $template->created_at->format('M d, Y \a\t g:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Updated:</td>
                        <td>{{ $template->updated_at->format('M d, Y \a\t g:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Times Used:</td>
                        <td>
                            <span class="badge bg-primary rounded-pill">{{ $template->usage_count }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Last Used:</td>
                        <td>
                            {{ $template->last_used_at ? $template->last_used_at->format('M d, Y \a\t g:i A') : 'Never used' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Template Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Usage Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <div class="fs-3 fw-bold text-primary">{{ $template->usage_count }}</div>
                            <small class="text-muted">Total Uses</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <div class="fs-6 fw-bold text-info">
                                {{ $template->last_used_at ? $template->last_used_at->diffForHumans() : 'Never' }}
                            </div>
                            <small class="text-muted">Last Used</small>
                        </div>
                    </div>
                </div>

                @if($template->usage_count > 0)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="bi bi-lightbulb me-1"></i>
                            This template has been used {{ $template->usage_count }}
                            {{ Str::plural('time', $template->usage_count) }} in campaigns.
                        </small>
                    </div>
                @else
                    <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded">
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This template hasn't been used in any campaigns yet.
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Template -->
        <div class="card border-danger">
            <div class="card-header bg-danger bg-opacity-10">
                <h6 class="card-title mb-0 text-danger">
                    <i class="bi bi-trash me-2"></i>Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Deleting a template is permanent and cannot be undone. All campaign history will remain intact.
                </p>
                <form action="{{ route('email-templates.destroy', $template) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-2"></i>Delete Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Duplicate template confirmation
    $('#duplicateForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Duplicate Template?',
            text: 'This will create a copy of "{{ $template->name }}" with "(Copy)" added to the name.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, duplicate it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Duplicating Template...',
                    text: 'Please wait while we create a copy.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });

    // Toggle template status confirmation
    $('#toggleForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const isActive = {{ $template->is_active ? 'true' : 'false' }};
        const action = isActive ? 'deactivate' : 'activate';
        const actionTitle = action.charAt(0).toUpperCase() + action.slice(1);

        Swal.fire({
            title: `${actionTitle} Template?`,
            text: isActive ?
                'This template will be hidden from campaign selections and marked as inactive.' :
                'This template will be available for use in campaigns and marked as active.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Yes, ${action} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Delete template confirmation
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Delete Template?',
            html: `
                <p>Are you sure you want to delete "<strong>{{ $template->name }}</strong>"?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This action cannot be undone!
                </p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting Template...',
                    text: 'Please wait while we delete the template.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
