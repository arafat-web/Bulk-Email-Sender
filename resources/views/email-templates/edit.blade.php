@extends('layouts.app')

@section('title', 'Edit Template - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Saved Templates</a></li>
    <li class="breadcrumb-item active">Edit Template</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Email Template
            </h1>
            <p class="page-subtitle">Modify your email template and keep it up to date.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>View Template
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

<!-- Error Messages -->
@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
            <strong>Please fix the following errors:</strong>
        </div>
        <ul class="mb-0 ms-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Template Details
                </h5>
                <div class="d-flex gap-2">
                    {!! $template->getStatusBadge() !!}
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Last updated {{ $template->updated_at->diffForHumans() }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.update', $template) }}" method="POST" id="templateForm">
                    @csrf
                    @method('PUT')

                    <!-- Template Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="bi bi-tag me-2"></i>Template Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $template->name) }}"
                               placeholder="Enter template name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Choose a descriptive name for easy identification.
                        </div>
                    </div>

                    <!-- Template Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="bi bi-card-text me-2"></i>Description
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Brief description of this template (optional)">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Subject -->
                    <div class="mb-4">
                        <label for="subject" class="form-label">
                            <i class="bi bi-type me-2"></i>Email Subject
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                               id="subject" name="subject" value="{{ old('subject', $template->subject) }}"
                               placeholder="Enter email subject line" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Body -->
                    <div class="mb-4">
                        <label for="body" class="form-label">
                            <i class="bi bi-card-text me-2"></i>Email Content
                            <span class="text-danger">*</span>
                        </label>
                        <textarea id="body" name="body" class="form-control @error('body') is-invalid @enderror"
                                  rows="12" placeholder="Compose your email content here..." required>{{ old('body', $template->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-palette me-1"></i>
                            Use the rich text editor to format your email with colors, fonts, and styling.
                        </div>
                    </div>

                    <!-- Template Status -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="bi bi-check-circle me-2"></i>Active Template
                            </label>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Active templates can be used in campaigns. Inactive templates are hidden.
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-save me-2"></i>Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Template Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Template Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="fs-4 fw-bold text-primary">{{ $template->usage_count }}</div>
                            <small class="text-muted">Times Used</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="fs-6 fw-bold text-info">
                                {{ $template->last_used_at ? $template->last_used_at->format('M d, Y') : 'Never' }}
                            </div>
                            <small class="text-muted">Last Used</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        Created {{ $template->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Template Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye me-2"></i>Preview Template
                    </a>
                    <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-files me-2"></i>Duplicate Template
                        </button>
                    </form>
                    <form action="{{ route('email-templates.toggle', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $template->is_active ? 'warning' : 'success' }} btn-sm w-100">
                            <i class="bi bi-{{ $template->is_active ? 'pause' : 'play' }}-circle me-2"></i>
                            {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>Live Preview
                </h6>
            </div>
            <div class="card-body">
                <div class="email-preview">
                    <div class="mb-2">
                        <strong>Subject:</strong>
                        <div id="preview-subject" class="text-muted">{{ $template->subject }}</div>
                    </div>
                    <div>
                        <strong>Content:</strong>
                        <div id="preview-body" class="text-muted mt-2" style="max-height: 200px; overflow-y: auto;">
                            {!! $template->body !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize TinyMCE editor with premium features
    tinymce.init({
        selector: '#body',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount template emoticons',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | link image media table | emoticons template | removeformat code fullscreen | help',
        menubar: false,
        branding: false,
        height: 350,
        content_style: 'body { font-family: Plus Jakarta Sans, sans-serif; font-size: 14px; line-height: 1.6; }',
        font_family_formats: 'Plus Jakarta Sans=Plus Jakarta Sans, sans-serif; Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times; Courier New=courier new,courier',
        fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt',
        image_advtab: true,
        link_context_toolbar: true,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
                updatePreview();
            });
        }
    });

    // Live preview
    function updatePreview() {
        const subject = $('#subject').val() || 'Your subject will appear here';
        const body = tinymce.get('body') ? tinymce.get('body').getContent() : 'Your email content will appear here';

        $('#preview-subject').text(subject);
        $('#preview-body').html(body);
    }

    $('#subject').on('input keyup', updatePreview);

    // Form validation
    $('#templateForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const subject = $('#subject').val().trim();
        const body = tinymce.get('body') ? tinymce.get('body').getContent().trim() : '';

        if (!name || !subject || !body) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: 'Please fill in all required fields (Name, Subject, and Content).',
                confirmButtonColor: '#6366f1'
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Updating Template...',
            text: 'Please wait while we save your changes.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Duplicate template confirmation
    $('form[action*="duplicate"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Duplicate Template?',
            text: 'This will create a copy of this template with "(Copy)" added to the name.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, duplicate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Toggle template status confirmation
    $('form[action*="toggle"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const isActive = {{ $template->is_active ? 'true' : 'false' }};
        const action = isActive ? 'deactivate' : 'activate';

        Swal.fire({
            title: `${action.charAt(0).toUpperCase() + action.slice(1)} Template?`,
            text: isActive ?
                'This template will be hidden from campaign selections.' :
                'This template will be available for use in campaigns.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Yes, ${action} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
