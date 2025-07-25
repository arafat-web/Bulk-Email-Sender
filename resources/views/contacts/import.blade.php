@extends('layouts.app')

@section('title', 'Import Contacts - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Email Contacts</a></li>
    <li class="breadcrumb-item active">Import Contacts</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Import Contacts</h1>
                    <p class="text-muted mb-0">Import contacts from Excel, CSV files or add manually</p>
                </div>
                <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Contacts
                </a>
            </div>

            <div class="row">
                <!-- File Import Section -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-upload me-2 text-primary"></i>Import from File
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-4">
                                    <label for="file" class="form-label text-dark fw-medium">Choose File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                           id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text text-muted">
                                        Supported formats: Excel (.xlsx, .xls) and CSV (.csv) files. Maximum size: 10MB
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tags Selection -->
                                <div class="mb-4">
                                    <label class="form-label text-dark fw-medium">Apply Tags to Imported Contacts</label>
                                    <div class="mb-2">
                                        <small class="text-muted">All imported contacts will be assigned the selected tags</small>
                                    </div>

                                    @if($tags->count() > 0)
                                        <div class="row g-2">
                                            @foreach($tags as $tag)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                               name="tags[]" value="{{ $tag->id }}"
                                                               id="import_tag_{{ $tag->id }}">
                                                        <label class="form-check-label text-dark" for="import_tag_{{ $tag->id }}">
                                                            <span class="badge me-2" style="background-color: {{ $tag->color }}; color: white;">
                                                                {{ $tag->name }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>No tags available. You can create tags to organize imported contacts.</span>
                                                <a href="{{ route('tags.create') }}" class="btn btn-sm btn-primary">Create Tag</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload me-2"></i>Import Contacts
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Instructions Section -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-info-circle me-2 text-primary"></i>Import Instructions
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="text-dark mb-3">File Format Requirements:</h6>
                            <ul class="list-unstyled text-dark small">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    First row should contain column headers
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Required column: <code>email</code>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Optional columns: <code>first_name</code>, <code>last_name</code>, <code>phone</code>, <code>company</code>, <code>notes</code>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Duplicate emails will be skipped
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Invalid email addresses will be skipped
                                </li>
                            </ul>

                            <div class="mt-4">
                                <h6 class="text-dark mb-3">Sample CSV Format:</h6>
                                <div class="bg-light p-3 rounded small border">
                                    <code class="text-dark">
email,first_name,last_name,company<br>
john@example.com,John,Doe,ABC Corp<br>
jane@example.com,Jane,Smith,XYZ Inc<br>
                                    </code>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="#" class="btn btn-outline-primary btn-sm w-100" onclick="downloadSampleCSV()">
                                    <i class="bi bi-download me-2"></i>Download Sample CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Add Section -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-plus-circle me-2 text-primary"></i>Quick Add
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Need to add individual contacts?</p>
                            <a href="{{ route('contacts.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-plus me-2"></i>Add Single Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control, .form-select {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .form-control:focus, .form-select:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: #6f42c1;
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }

    .form-control::file-selector-button {
        background-color: #6f42c1;
        color: white;
        border: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem 0 0 0.375rem;
    }

    .form-check-input:checked {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
function downloadSampleCSV() {
    const csvContent = "email,first_name,last_name,phone,company,notes\n" +
                      "john.doe@example.com,John,Doe,+1234567890,ABC Corporation,Sales contact\n" +
                      "jane.smith@example.com,Jane,Smith,+0987654321,XYZ Inc,Marketing lead\n" +
                      "                      "contact@example.com,,,+1122334455,Example LLC,General inquiry";

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'contacts_sample.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
@endpush

@push('styles')
<style>
    .form-control, .form-select {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: var(--primary-color);
        color: #374151;
        box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
    }

    .card {
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #6d28d9 100%);
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    code {
        background-color: #f3f4f6;
        color: #374151;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
function downloadSampleCSV() {
    const csvContent = "email,first_name,last_name,phone,company,notes\n" +
                      "john.doe@example.com,John,Doe,+1234567890,ABC Corporation,Sales contact\n" +
                      "jane.smith@example.com,Jane,Smith,+0987654321,XYZ Inc,Marketing lead\n" +
                      "contact@example.com,,,+1122334455,Example LLC,General inquiry";

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'contacts_sample.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
@endpush
@ e n d s e c t i o n 
 
 
