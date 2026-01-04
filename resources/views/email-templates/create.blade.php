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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-plus me-2"></i>Template Details
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#guidelinesModal">
                        <i class="bi bi-lightbulb me-1"></i>View Guidelines
                    </button>
                </div>
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
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body') }}</textarea>
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
                                  rows="20" placeholder="Enter your HTML/CSS code here..." style="font-size: 13px;">{{ old('body') }}</textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Full HTML/CSS support. Use inline styles for best email client compatibility.
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

<!-- Guidelines Modal -->
<div class="modal fade" id="guidelinesModal" tabindex="-1" aria-labelledby="guidelinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guidelinesModalLabel">
                    <i class="bi bi-lightbulb me-2 text-primary"></i>Template Guidelines & Best Practices
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>Best Practices
                        </h6>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Clear Template Names</strong>
                                <p class="text-muted small mb-0">Use descriptive names for easy identification</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Compelling Subjects</strong>
                                <p class="text-muted small mb-0">Write subject lines that grab attention</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Concise Content</strong>
                                <p class="text-muted small mb-0">Keep emails focused and easy to read</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Test First</strong>
                                <p class="text-muted small mb-0">Always test before sending campaigns</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-code-slash text-primary me-2"></i>Email Design Tips
                        </h6>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Use Tables</strong>
                                <p class="text-muted small mb-0">Tables ensure better email client compatibility</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Inline Styles</strong>
                                <p class="text-muted small mb-0">Use inline CSS for best rendering</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Mobile Responsive</strong>
                                <p class="text-muted small mb-0">Design for mobile-first viewing</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Alt Text</strong>
                                <p class="text-muted small mb-0">Add alt text to all images</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="alert alert-info mb-0">
                    <h6 class="alert-heading">
                        <i class="bi bi-palette me-2"></i>Visual Designer Tips
                    </h6>
                    <ul class="mb-0 small">
                        <li>Drag components from the left panel onto the canvas</li>
                        <li>Select elements to customize styles in the right panel</li>
                     7  <li>Use pre-built blocks for faster design</li>
                        <li>Switch to code mode for advanced HTML/CSS editing</li>
                        <li>Preview on different devices using the toolbar</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Got it!
                </button   <strong>Content:</strong>
                        <div id="preview-body" class="text-muted mt-2" style="max-height: 200px; overflow-y: auto;">
                            Your email content will appear here
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        updatePreview();
    });

    // Live preview
    function updatePreview() {
        const subject = $('#subject').val() || 'Your subject will appear here';
        let body;
        
        if ($('#visualMode').is(':checked')) {
            body = $('#body').val() || 'Your email content will appear here';
        } else {
            body = $('#bodyCode').val() || 'Your email content will appear here';
        }

        $('#preview-subject').text(subject);
        $('#preview-body').html(body);
    }

    $('#subject').on('input keyup', updatePreview);

    // Form validation
    $('#templateForm').on('submit', function(e) {
        // Ensure content is synced before submission
        if ($('#visualMode').is(':checked') && editor) {
            const html = editor.getHtml();
            const css = editor.getCss();
            $('#body').val(`<style>${css}</style>${html}`);
        } else {
            $('#body').val($('#bodyCode').val());
        }

        const name = $('#name').val().trim();
        const subject = $('#subject').val().trim();
        const body = $('#body').val().trim();

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
