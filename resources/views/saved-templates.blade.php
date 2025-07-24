@extends('layouts.app')

@section('title', 'Saved Templates - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Email Marketing</li>
    <li class="breadcrumb-item active">Saved Templates</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Saved Email Templates</h1>
            <p class="page-subtitle">Create, manage and reuse your email templates for future campaigns.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="bi bi-plus-circle me-2"></i>Create Template
            </button>
        </div>
    </div>
</div>

<!-- Under Construction Alert -->
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-tools fs-4 me-3"></i>
    <div class="flex-grow-1">
        <h6 class="alert-heading mb-1">Feature Under Development</h6>
        <p class="mb-0">This section is currently under construction. The saved template functionality will be available soon. For now, you can use the Instant Campaign for your email marketing.</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-send text-primary fs-3"></i>
                </div>
                <h5 class="card-title">Instant Campaign</h5>
                <p class="card-text text-muted">Launch immediate email campaigns with Excel file uploads.</p>
                <a href="{{ route('instant.campaign.create') }}" class="btn btn-primary">
                    <i class="bi bi-lightning me-2"></i>Launch Campaign
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-envelope-gear text-success fs-3"></i>
                </div>
                <h5 class="card-title">Email Accounts</h5>
                <p class="card-text text-muted">Manage your SMTP email accounts and configurations.</p>
                <a href="{{ route('email-accounts.index') }}" class="btn btn-success">
                    <i class="bi bi-gear me-2"></i>Manage Accounts
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="bg-info bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-graph-up text-info fs-3"></i>
                </div>
                <h5 class="card-title">View Reports</h5>
                <p class="card-text text-muted">Check your email campaign statistics and performance.</p>
                <a href="{{ route('home') }}" class="btn btn-info">
                    <i class="bi bi-bar-chart me-2"></i>View Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Future Templates Preview -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-bookmark me-2"></i>Saved Templates (Coming Soon)
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Template Preview Cards -->
            @for($i = 1; $i <= 3; $i++)
            <div class="col-md-4 mb-4">
                <div class="card border-dashed h-100" style="opacity: 0.6;">
                    <div class="card-body text-center">
                        <div class="bg-light rounded-circle mx-auto mb-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-text text-muted fs-4"></i>
                        </div>
                        <h6 class="card-title text-muted">Template Name {{ $i }}</h6>
                        <p class="card-text text-muted small">Email template description will appear here...</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                            <button class="btn btn-sm btn-outline-primary" disabled>
                                <i class="bi bi-send me-1"></i>Use
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Created: --
                            <i class="bi bi-send ms-2 me-1"></i>Used: -- times
                        </small>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <div class="bg-light rounded p-4">
                <i class="bi bi-plus-circle display-1 text-muted mb-3"></i>
                <h5 class="text-muted">No Templates Yet</h5>
                <p class="text-muted">Templates will be displayed here once the feature is complete. Stay tuned for updates!</p>
            </div>
        </div>
    </div>
</div>

<!-- Create Template Modal (Placeholder) -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-labelledby="createTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTemplateModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Create New Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Feature Coming Soon!</strong> The template creation functionality is currently under development. Please use the Instant Campaign for now.
                </div>

                <!-- Preview of future form -->
                <form>
                    <div class="mb-3">
                        <label for="templateName" class="form-label">Template Name</label>
                        <input type="text" class="form-control" id="templateName" placeholder="Enter template name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="templateSubject" class="form-label">Email Subject</label>
                        <input type="text" class="form-control" id="templateSubject" placeholder="Enter email subject" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="templateBody" class="form-label">Email Content</label>
                        <textarea class="form-control" id="templateBody" rows="8" placeholder="Enter email content" disabled></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" disabled>
                    <i class="bi bi-save me-2"></i>Save Template
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-dashed {
    border-style: dashed !important;
    border-width: 2px !important;
    border-color: #dee2e6 !important;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Animate cards on load
    $('.card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });

    // Show coming soon message when trying to create template
    $('#createTemplateModal').on('show.bs.modal', function() {
        console.log('Template creation feature coming soon!');
    });
});
</script>
@endpush
@endsection
