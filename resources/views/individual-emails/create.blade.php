@extends('layouts.app')

@section('title', 'Send Individual Emails - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Send Individual Emails</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-envelope-heart text-primary me-2"></i>
                Send Individual Emails
            </h1>
            <p class="page-subtitle">Send personalized emails to specific recipients by entering their email addresses.</p>
        </div>
        <div class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2">
            <i class="bi bi-person-lines-fill me-1"></i>Individual Messaging
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form id="individualEmailForm" method="POST" action="{{ route('individual-emails.send') }}">
            @csrf

            <!-- Email Configuration -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Email Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_account_id" class="form-label fw-semibold">
                                <i class="bi bi-envelope-at me-1"></i>Send From Account
                            </label>
                            <select class="form-select @error('email_account_id') is-invalid @enderror"
                                    id="email_account_id"
                                    name="email_account_id"
                                    required>
                                <option value="">Select Email Account</option>
                                @foreach($emailAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('email_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->email }} ({{ $account->from_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('email_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="send_type" class="form-label fw-semibold">
                                <i class="bi bi-send me-1"></i>Sending Type
                            </label>
                            <select class="form-select" id="send_type" name="send_type" required>
                                <option value="individual" {{ old('send_type', 'individual') == 'individual' ? 'selected' : '' }}>
                                    Individual Emails (Each recipient gets separate email)
                                </option>
                                <option value="bulk" {{ old('send_type') == 'bulk' ? 'selected' : '' }}>
                                    Bulk Email (All recipients in one email)
                                </option>
                            </select>
                            <small class="text-muted">Individual emails provide better privacy, bulk is faster for announcements</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>Recipients
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="validateEmails">
                        <i class="bi bi-check-circle me-1"></i>Validate Emails
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="recipients" class="form-label fw-semibold">
                            <i class="bi bi-envelope-plus me-1"></i>Email Addresses
                        </label>
                        <textarea class="form-control @error('recipients') is-invalid @enderror"
                                  id="recipients"
                                  name="recipients"
                                  rows="6"
                                  placeholder="Enter email addresses separated by commas, semicolons, or new lines:&#10;&#10;example1@email.com&#10;example2@email.com, example3@email.com&#10;example4@email.com; example5@email.com"
                                  required>{{ old('recipients') }}</textarea>
                        @error('recipients')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            You can separate email addresses using commas (,), semicolons (;), or new lines
                        </small>
                    </div>

                    <!-- Email Validation Results -->
                    <div id="emailValidation" class="d-none">
                        <div class="alert alert-info border-0" style="background: rgba(13, 202, 240, 0.1);">
                            <h6 class="alert-heading">
                                <i class="bi bi-info-circle me-1"></i>Email Validation Results
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-success" id="validCount">0</div>
                                        <small class="text-muted">Valid Emails</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-danger" id="invalidCount">0</div>
                                        <small class="text-muted">Invalid Emails</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-primary" id="totalCount">0</div>
                                        <small class="text-muted">Total Processed</small>
                                    </div>
                                </div>
                            </div>
                            <div id="invalidEmailsList" class="mt-3 d-none">
                                <h6 class="text-danger">Invalid Emails:</h6>
                                <div class="invalid-emails-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Content -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Email Content
                    </h5>
                    @if($templates->count() > 0)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-file-earmark-text me-1"></i>Use Template
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($templates as $template)
                                    <li>
                                        <a class="dropdown-item template-option"
                                           href="#"
                                           data-subject="{{ $template->subject }}"
                                           data-body="{{ base64_encode($template->body) }}">
                                            {{ $template->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>Subject Line
                        </label>
                        <input type="text"
                               class="form-control @error('subject') is-invalid @enderror"
                               id="subject"
                               name="subject"
                               value="{{ old('subject') }}"
                               placeholder="Enter email subject"
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1"></i>Email Content
                        </label>
                        <textarea class="form-control @error('body') is-invalid @enderror"
                                  id="body"
                                  name="body"
                                  rows="12"
                                  required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-warning" id="resetForm">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Send Emails
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Quick Tips -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb text-warning me-2"></i>Quick Tips
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                    <div>
                        <small class="fw-semibold">Individual vs Bulk</small>
                        <p class="text-muted mb-0 small">Individual emails provide better privacy, while bulk emails are faster for announcements.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                    <div>
                        <small class="fw-semibold">Email Validation</small>
                        <p class="text-muted mb-0 small">Always validate your email list before sending to avoid bounces.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                    <div>
                        <small class="fw-semibold">Use Templates</small>
                        <p class="text-muted mb-0 small">Save time by using pre-created email templates for common messages.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                    <div>
                        <small class="fw-semibold">Format Support</small>
                        <p class="text-muted mb-0 small">Separate emails with commas, semicolons, or line breaks for flexibility.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Account Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle text-info me-2"></i>Email Accounts
                </h6>
            </div>
            <div class="card-body">
                @if($emailAccounts->count() > 0)
                    @foreach($emailAccounts as $account)
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                <i class="bi bi-envelope text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $account->from_name }}</div>
                                <div class="text-muted small">{{ $account->email }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-exclamation-triangle display-6 mb-2"></i>
                        <p class="mb-0">No email accounts configured</p>
                        <a href="{{ route('email-accounts.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-plus me-1"></i>Add Account
                        </a>
                    </div>
                @endif
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
        height: 300,
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
    $('.template-option').on('click', function(e) {
        e.preventDefault();
        const subject = $(this).data('subject');
        const body = atob($(this).data('body'));

        $('#subject').val(subject);
        tinymce.get('body').setContent(body);

        Swal.fire({
            icon: 'success',
            title: 'Template Applied!',
            text: 'The subject and content have been filled with the selected template.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    // Email validation
    $('#validateEmails').on('click', function() {
        const recipients = $('#recipients').val().trim();

        if (!recipients) {
            Swal.fire({
                icon: 'warning',
                title: 'No Emails Entered',
                text: 'Please enter some email addresses first.'
            });
            return;
        }

        $.ajax({
            url: '{{ route("individual-emails.validate") }}',
            method: 'POST',
            data: {
                recipients: recipients,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#validCount').text(response.valid_count);
                $('#invalidCount').text(response.invalid_count);
                $('#totalCount').text(response.valid_count + response.invalid_count);

                if (response.invalid_count > 0) {
                    const invalidContainer = $('.invalid-emails-container');
                    invalidContainer.empty();
                    response.invalid_emails.forEach(function(email) {
                        invalidContainer.append(`<span class="badge bg-danger me-1 mb-1">${email}</span>`);
                    });
                    $('#invalidEmailsList').removeClass('d-none');
                } else {
                    $('#invalidEmailsList').addClass('d-none');
                }

                $('#emailValidation').removeClass('d-none');
            }
        });
    });

    // Form submission
    $('#individualEmailForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Update TinyMCE content
        tinymce.get('body').save();
        formData.set('body', tinymce.get('body').getContent());

        Swal.fire({
            title: 'Sending Emails...',
            text: 'Please wait while we process and queue your emails.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    let message = `Successfully queued ${response.summary.total_emails} emails!`;

                    if (response.summary.invalid_emails > 0) {
                        message += `\n\n${response.summary.invalid_emails} invalid email addresses were skipped.`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Emails Queued Successfully!',
                        text: message,
                        confirmButtonColor: '#8b5cf6'
                    }).then(() => {
                        // Reset form
                        $('#individualEmailForm')[0].reset();
                        tinymce.get('body').setContent('');
                        $('#emailValidation').addClass('d-none');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Send Emails',
                        text: response.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = 'An error occurred while sending emails.';

                if (response && response.message) {
                    errorMessage = response.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    // Reset form
    $('#resetForm').on('click', function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'This will clear all entered data. Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#individualEmailForm')[0].reset();
                tinymce.get('body').setContent('');
                $('#emailValidation').addClass('d-none');

                Swal.fire({
                    icon: 'success',
                    title: 'Form Reset!',
                    text: 'All fields have been cleared.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
});
</script>
@endpush
@endsection
