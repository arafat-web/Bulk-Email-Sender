@extends('layouts.app')

@section('title', 'Create Instant Campaign - BES')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Instant Campaign</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-send text-primary me-2"></i>Create Instant Campaign
            </h1>
            <p class="page-subtitle">Send bulk emails instantly by uploading an Excel file with email addresses.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email-templates.index') }}" class="btn btn-outline-info">
                <i class="bi bi-file-earmark-text me-2"></i>Manage Templates
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
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                    <i class="bi bi-upload me-2"></i>Campaign Setup
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('instant.campaign.import') }}" method="POST" enctype="multipart/form-data" id="campaignForm">
                    @csrf

                    <!-- Template Selection -->
                    @if($templates->count() > 0)
                        <div class="mb-4">
                            <label for="template_select" class="form-label">
                                <i class="bi bi-file-earmark-text me-2"></i>Use Email Template
                                <span class="text-muted">(Optional)</span>
                            </label>
                            <select class="form-select" id="template_select" name="template_id">
                                <option value="">Select a template (optional)</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}"
                                            data-subject="{{ $template->subject }}"
                                            data-body="{{ base64_encode($template->body) }}"
                                            {{ $selectedTemplate && $selectedTemplate->id == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                        @if($template->usage_count > 0)
                                            (Used {{ $template->usage_count }} times)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Select a saved template to pre-fill the subject and content fields.
                            </div>
                        </div>
                    @endif

                    <!-- Excel File Upload -->
                    <div class="mb-4">
                        <label for="file" class="form-label">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Excel File
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".xlsx,.xls" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload an Excel file with email addresses in the 3rd column (Column C). Max size: 10MB.
                        </div>
                    </div>

                    <!-- Email Subject -->
                    <div class="mb-4">
                        <label for="subject" class="form-label">
                            <i class="bi bi-type me-2"></i>Email Subject
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                               id="subject" name="subject"
                               value="{{ old('subject', $selectedTemplate ? $selectedTemplate->subject : '') }}"
                               placeholder="Enter your email subject line" required>
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
                                  rows="12" placeholder="Compose your email message here..." required>{{ old('body', $selectedTemplate ? $selectedTemplate->body : '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-palette me-1"></i>
                            Use the rich text editor to format your email with colors, fonts, and styling.
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-outline-secondary" id="resetForm">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="bi bi-send me-2"></i>Send Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Campaign Guidelines -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Campaign Guidelines
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Email addresses should be in Column C (3rd column)</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Maximum 10,000 email addresses per campaign</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Use .xlsx or .xls file formats only</small>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Write compelling subject lines for better open rates</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Format Example -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Excel File Format
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>A</th>
                                <th>B</th>
                                <th class="bg-primary text-white">C</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td>Company</td>
                                <td class="fw-bold">john@example.com</td>
                            </tr>
                            <tr>
                                <td>Jane</td>
                                <td>ABC Corp</td>
                                <td class="fw-bold">jane@company.com</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Email addresses must be in Column C (highlighted in blue).
                </small>
            </div>
        </div>

        @if($templates->count() > 0)
            <!-- Quick Templates -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Templates
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @foreach($templates->take(3) as $template)
                            <button type="button" class="btn btn-outline-primary btn-sm text-start template-quick-btn"
                                    data-template-id="{{ $template->id }}"
                                    data-subject="{{ $template->subject }}"
                                    data-body="{{ base64_encode($template->body) }}">
                                <div class="fw-bold">{{ $template->name }}</div>
                                <small class="text-muted">{{ Str::limit($template->subject, 40) }}</small>
                            </button>
                        @endforeach
                        @if($templates->count() > 3)
                            <a href="{{ route('email-templates.index') }}" class="btn btn-link btn-sm">
                                View all {{ $templates->count() }} templates
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
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
            });
        }
    });

    // Template selection handler
    $('#template_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const subject = selectedOption.data('subject');
            const body = atob(selectedOption.data('body')); // Decode base64

            $('#subject').val(subject);

            // Update TinyMCE editor
            tinymce.get('body').setContent(body);

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Template Applied!',
                text: 'The subject and content have been filled with the selected template.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Quick template buttons
    $('.template-quick-btn').on('click', function() {
        const templateId = $(this).data('template-id');
        const subject = $(this).data('subject');
        const body = atob($(this).data('body'));

        $('#template_select').val(templateId);
        $('#subject').val(subject);
        $('#body').val(body);

        // Update rich text editor
        $('#body').next('.richText-editor').html(body);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Template Applied!',
            text: 'Quick template has been applied to your campaign.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    // Form validation and submission
    $('#campaignForm').on('submit', function(e) {
        const file = $('#file')[0].files[0];
        const subject = $('#subject').val().trim();
        const body = $('#body').val().trim();

        if (!file || !subject || !body) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: 'Please fill in all required fields and upload an Excel file.',
                confirmButtonColor: '#6366f1'
            });
            return false;
        }

        // File validation
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Size in MB
            const allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

            if (fileSize > 10) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Please upload a file smaller than 10MB.',
                    confirmButtonColor: '#6366f1'
                });
                return false;
            }

            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a valid Excel file (.xlsx or .xls).',
                    confirmButtonColor: '#6366f1'
                });
                return false;
            }
        }

        // Show loading
        $('#submitBtn').html('<i class="spinner-border spinner-border-sm me-2"></i>Processing Campaign...').prop('disabled', true);

        Swal.fire({
            title: 'Processing Campaign...',
            text: 'Please wait while we process your campaign. This may take a few moments.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Reset form handler
    $('#resetForm').on('click', function() {
        $('#template_select').val('');
        tinymce.get('body').setContent('');
    });

    // Auto-apply template if pre-selected
    @if($selectedTemplate)
        Swal.fire({
            icon: 'info',
            title: 'Template Pre-loaded',
            text: 'The "{{ $selectedTemplate->name }}" template has been pre-loaded for you.',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});
</script>
@endpush
@endsection
