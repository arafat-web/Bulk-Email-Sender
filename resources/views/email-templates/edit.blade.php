@extends('layouts.app')

@section('title', 'Edit Template - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Templates</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit: {{ $template->name }}</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Used {{ $template->usage_count }} times &middot; Last updated {{ $template->updated_at->diffForHumans() }}</p>
    </div>
    <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary btn-sm">Back to Templates</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Template Content</h5></div>
            <div class="card-body">
                <form action="{{ route('email-templates.update', $template) }}" method="POST" id="templateForm">
                    @csrf @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Template Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="subject" class="form-label">Email Subject <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $template->subject) }}" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $template->description) }}" placeholder="Brief description">
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">Email Body <span style="color:#ef4444;">*</span></label>
                        <textarea id="body" name="body" class="form-control @error('body') is-invalid @enderror" rows="14">{{ old('body', $template->body) }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active" style="font-size:13px;">Active (available for campaigns)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({ selector: '#body', height: 420, menubar: false, branding: false,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code preview fullscreen | removeformat',
            content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }',
            setup: function(ed){ ed.on('change', function(){ ed.save(); }); }
        });
    }
    $('#templateForm').on('submit', function(e){
        if (typeof tinymce !== 'undefined' && tinymce.get('body')) tinymce.get('body').save();
        if (!$('#body').val().replace(/<[^>]*>/g,'').trim()) { e.preventDefault(); Swal.fire({icon:'error',title:'Missing email body',text:'Please enter template content.'}); return false; }
    });
});
</script>
@endpush
