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
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Excel/CSV File
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload an Excel file using the standard format: Given Name, Family Name, eMail Address, Company, Phone, Notes. Max size: 10MB.
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

                    <!-- Editor Mode Toggle -->
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="editorMode" id="visualMode" checked autocomplete="off">
                            <label class="btn btn-outline-primary" for="visualMode">
                                <i class="bi bi-palette me-1"></i>Visual Designer
                            </label>

                            <input type="radio" class="btn-check" name="editorMode" id="codeMode" autocomplete="off">
                            <label class="btn btn-outline-primary" for="codeMode">
                                <i class="bi bi-code-slash me-1"></i>HTML/CSS Code
                            </label>
                        </div>
                        <small class="text-muted ms-2">Choose your preferred editing mode</small>
                    </div>

                    <!-- Email Body - Visual Designer -->
                    <div class="mb-4" id="visualEditorContainer">
                        <label class="form-label">
                            <i class="bi bi-brush me-2"></i>Email Design
                            <span class="text-danger">*</span>
                        </label>
                        <div id="gjs" style="height: 0px;"></div>
                        <textarea id="body" name="body" class="form-control d-none">{{ old('body', $selectedTemplate ? $selectedTemplate->body : '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Body - Code Editor -->
                    <div class="mb-4 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">
                            <i class="bi bi-code-square me-2"></i>HTML/CSS Code
                            <span class="text-danger">*</span>
                        </label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror"
                                  rows="20" placeholder="Enter your HTML/CSS code here..." style="font-size: 13px;">{{ old('body', $selectedTemplate ? $selectedTemplate->body : '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Full HTML/CSS support. Use inline styles for best email client compatibility.
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
                        <small>Use standard format: Given Name, Family Name, eMail Address, Company, Phone, Notes</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <small>Email address is required in Column C, other fields are optional</small>
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
                        <small>Use .xlsx, .xls, or .csv file formats</small>
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
                    <i class="bi bi-table me-2"></i>Excel/CSV File Format
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
                                <th>D</th>
                                <th>E</th>
                                <th>F</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Given Name</td>
                                <td>Family Name</td>
                                <td class="fw-bold bg-primary text-white">eMail Address</td>
                                <td>Company</td>
                                <td>Phone</td>
                                <td>Notes</td>
                            </tr>
                            <tr>
                                <td>John</td>
                                <td>Doe</td>
                                <td class="fw-bold">john@example.com</td>
                                <td>ABC Corp</td>
                                <td>+1234567890</td>
                                <td>Sales contact</td>
                            </tr>
                            <tr>
                                <td>Crystal</td>
                                <td>Chantal-Leer</td>
                                <td class="fw-bold">ccl@example.com</td>
                                <td>Sample Company</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Use the standard format: Given Name, Family Name, eMail Address, Company, Phone, Notes. Email address is required in column C.
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
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($templates->take(6) as $template)
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary template-quick-btn"
                                    data-template-id="{{ $template->id }}"
                                    data-subject="{{ $template->subject }}"
                                    data-body="{{ base64_encode($template->body) }}"
                                    title="Click to apply: {{ $template->name }}">
                                <i class="bi bi-file-text me-1"></i>{{ Str::limit($template->name, 15) }}
                            </button>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Click any template to quickly apply it to your campaign.
                    </small>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
<link href="https://unpkg.com/grapesjs-preset-newsletter/dist/grapesjs-preset-newsletter.min.css" rel="stylesheet">
<style>
    .gjs-one-bg {
        background-color: #1e293b;
    }
    .gjs-two-color {
        color: rgba(255, 255, 255, 0.7);
    }
    .gjs-three-bg {
        background-color: #334155;
        color: white;
    }
    .gjs-four-color,
    .gjs-four-color-h:hover {
        color: #6366f1;
    }
    #gjs {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter"></script>
<script>
$(document).ready(function() {
    let editor;
    
    // Initialize GrapeJS Email Builder
    function initGrapesJS() {
        editor = grapesjs.init({
            container: '#gjs',
            height: '600px',
            width: 'auto',
            plugins: ['gjs-preset-newsletter'],
            pluginsOpts: {
                'gjs-preset-newsletter': {
                    modalTitleImport: 'Import Template',
                    modalLabelImport: 'Paste your HTML here',
                    modalBtnImport: 'Import',
                    modalTitleExport: 'Export Template',
                    modalLabelExport: 'Copy the code below',
                    codeViewerTheme: 'material',
                    importPlaceholder: '<table>...</table>',
                    cellStyle: {
                        'font-size': '14px',
                        'font-family': 'Arial, sans-serif',
                        'line-height': '1.6'
                    }
                }
            },
            storageManager: false,
            assetManager: {
                embedAsBase64: false,
                assets: []
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'
                ],
                scripts: []
            }
        });

        // Add custom email blocks
        editor.BlockManager.add('header-block', {
            label: 'Header',
            category: 'Email Sections',
            content: `
                <table style="width: 100%; background: #6366f1; padding: 30px; text-align: center;">
                    <tr>
                        <td>
                            <h1 style="color: white; margin: 0; font-size: 32px;">Your Company</h1>
                            <p style="color: white; margin: 10px 0 0;">Your tagline here</p>
                        </td>
                    </tr>
                </table>
            `
        });

        editor.BlockManager.add('cta-button', {
            label: 'CTA Button',
            category: 'Email Sections',
            content: `
                <table style="width: 100%; padding: 20px; text-align: center;">
                    <tr>
                        <td>
                            <a href="#" style="display: inline-block; padding: 15px 40px; background: #6366f1; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
                                Click Here
                            </a>
                        </td>
                    </tr>
                </table>
            `
        });

        editor.BlockManager.add('footer-block', {
            label: 'Footer',
            category: 'Email Sections',
            content: `
                <table style="width: 100%; background: #f8fafc; padding: 30px; text-align: center; font-size: 12px; color: #64748b;">
                    <tr>
                        <td>
                            <p style="margin: 0 0 10px;">© 2025 Your Company. All rights reserved.</p>
                            <p style="margin: 0;">
                                <a href="#" style="color: #6366f1; text-decoration: none;">Unsubscribe</a> | 
                                <a href="#" style="color: #6366f1; text-decoration: none;">Privacy Policy</a>
                            </p>
                        </td>
                    </tr>
                </table>
            `
        });

        // Update hidden textarea when editor changes
        editor.on('update', function() {
            const html = editor.getHtml();
            const css = editor.getCss();
            const fullContent = `<style>${css}</style>${html}`;
            $('#body').val(fullContent);
        });

        // Load existing content if any
        const existingContent = $('#body').val();
        if (existingContent) {
            editor.setComponents(existingContent);
        }
    }

    // Initialize editor on page load
    initGrapesJS();

    // Editor mode switching
    $('input[name="editorMode"]').on('change', function() {
        if ($('#visualMode').is(':checked')) {
            $('#visualEditorContainer').removeClass('d-none');
            $('#codeEditorContainer').addClass('d-none');
            
            // Sync code to visual
            const codeContent = $('#bodyCode').val();
            if (codeContent && editor) {
                editor.setComponents(codeContent);
            }
        } else {
            $('#codeEditorContainer').removeClass('d-none');
            $('#visualEditorContainer').addClass('d-none');
            
            // Sync visual to code
            if (editor) {
                const html = editor.getHtml();
                const css = editor.getCss();
                $('#bodyCode').val(`<style>${css}</style>\n${html}`);
            }
        }
    });

    // Sync code editor changes to hidden field
    $('#bodyCode').on('input', function() {
        $('#body').val($(this).val());
    });

    // Template selection handler
    $('#template_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const subject = selectedOption.data('subject');
            const body = atob(selectedOption.data('body')); // Decode base64

            $('#subject').val(subject);

            // Update visual editor or code editor based on active mode
            if ($('#visualMode').is(':checked') && editor) {
                editor.setComponents(body);
            } else {
                $('#bodyCode').val(body);
            }
            $('#body').val(body);

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

        // Update visual editor or code editor based on active mode
        if ($('#visualMode').is(':checked') && editor) {
            editor.setComponents(body);
        } else {
            $('#bodyCode').val(body);
        }
        $('#body').val(body);

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
        // Ensure content is synced before submission
        if ($('#visualMode').is(':checked') && editor) {
            const html = editor.getHtml();
            const css = editor.getCss();
            $('#body').val(`<style>${css}</style>${html}`);
        } else {
            $('#body').val($('#bodyCode').val());
        }

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
            const allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];

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
                    text: 'Please upload a valid Excel (.xlsx, .xls) or CSV (.csv) file.',
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
        if (editor) {
            editor.setComponents('');
        }
        $('#bodyCode').val('');
        $('#body').val('');
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
