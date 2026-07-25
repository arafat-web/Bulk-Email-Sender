@extends('layouts.app')

@section('title', 'Import Contacts - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Import Contacts</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Import from Excel, CSV or add manually</p>
    </div>
    <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary btn-sm">Back to Contacts</a>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Import from File</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label">Choose File <span style="color:#ef4444;">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Excel (.xlsx, .xls) or CSV (.csv). Max 10MB.</div>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apply Tags</label>
                        @if($tags->count() > 0)
                            <div class="row g-2">
                                @foreach($tags as $tag)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="tags[]" value="{{ $tag->id }}" id="import_tag_{{ $tag->id }}">
                                            <label class="form-check-label" for="import_tag_{{ $tag->id }}" style="font-size:13px;">
                                                <span class="badge me-1" style="background-color:{{ $tag->color }};color:#fff;">{{ $tag->name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p style="font-size:13px;color:#94a3b8;">No tags yet. <a href="{{ route('tags.create') }}">Create one</a>.</p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Import Contacts</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">File Format</h5></div>
            <div class="card-body" style="font-size:13px;">
                <ul class="list-unstyled mb-3" style="display:flex;flex-direction:column;gap:8px;">
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> First row must contain column headers</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Required: <code>eMail Address</code></li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Optional: Given Name, Family Name, Company, Phone, Notes</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Duplicate &amp; invalid emails are skipped</li>
                </ul>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-bottom:12px;">
                    <code style="font-size:11px;color:#334155;word-break:break-all;">
Given Name,Family Name,eMail Address,Company,Phone,Notes<br>
John,Doe,john@example.com,ABC Corp,+1234567890,Sales
                    </code>
                </div>
                <a href="#" class="btn btn-outline-primary btn-sm w-100" onclick="downloadSampleCSV()">Download Sample CSV</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title">Quick Add</h5></div>
            <div class="card-body">
                <p style="font-size:13px;color:#64748b;margin-bottom:12px;">Need to add contacts one at a time?</p>
                <a href="{{ route('contacts.create') }}" class="btn btn-outline-primary btn-sm w-100">Add Single Contact</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function downloadSampleCSV() {
    var csv = "Given Name,Family Name,eMail Address,Company,Phone,Notes\n" +
              "John,Doe,john@example.com,ABC Corp,+1234567890,Sales contact\n" +
              "Jane,Smith,jane@example.com,XYZ Inc,+0987654321,Marketing lead\n" +
              "Contact,,contact@example.com,Example LLC,+1122334455,General inquiry";
    var blob = new Blob([csv], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'contacts_sample.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
@endpush
