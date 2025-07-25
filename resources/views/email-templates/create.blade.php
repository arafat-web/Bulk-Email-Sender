@extends('layouts.app')

@section('title', 'Create Template - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Saved Templates</a></li>
    <li class="breadcrumb-item active">Create Template</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-plus-circle text-primary me-2"></i>Create Email Template
            </h1>
            <p class="page-subtitle">Create a reusable email template for your marketing campaigns.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Templates
            </a>
        </div>
    </div>
</div>

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
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-plus me-2"></i>Template Details
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.store') }}" method="POST" id="templateForm">
                    @csrf

                    <!-- Template Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="bi bi-tag me-2"></i>Template Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}"
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
                                  placeholder="Brief description of this template (optional)">{{ old('description') }}</textarea>
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
                               id="subject" name="subject" value="{{ old('subject') }}"
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
                                  rows="12" placeholder="Compose your email content here..." required>{{ old('body') }}</textarea>
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
                                   {{ old('is_active') ? 'checked' : 'checked' }}>
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
                        <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>Create Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Template Guidelines -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Template Guidelines
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Use clear, descriptive template names</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Write compelling subject lines</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Keep content concise and engaging</small>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Test templates before using in campaigns</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>Template Preview
                </h6>
            </div>
            <div class="card-body">
                <div class="email-preview">
                    <div class="mb-2">
                        <strong>Subject:</strong>
                        <div id="preview-subject" class="text-muted">Your subject will appear here</div>
                    </div>
                    <div>
                        <strong>Content:</strong>
                        <div id="preview-body" class="text-muted mt-2" style="max-height: 200px; overflow-y: auto;">
                            Your email content will appear here
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
            title: 'Creating Template...',
            text: 'Please wait while we save your template.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>
@endpush
@endsection
