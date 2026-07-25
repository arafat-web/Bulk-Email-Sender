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
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="editorMode" id="visualMode" checked autocomplete="off">
                            <label class="btn btn-outline-primary" for="visualMode"><i class="bi bi-palette me-1"></i>Visual Designer</label>
                            <input type="radio" class="btn-check" name="editorMode" id="codeMode" autocomplete="off">
                            <label class="btn btn-outline-primary" for="codeMode"><i class="bi bi-code-slash me-1"></i>HTML Code</label>
                        </div>
                    </div>

                    <div class="mb-3" id="visualEditorContainer">
                        <label class="form-label">Email Body <span style="color:#ef4444;">*</span></label>
                        <div id="gjs"></div>
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body', $template->body) }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">HTML Code <span style="color:#ef4444;">*</span></label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror" rows="18" style="font-size:13px;">{{ old('body', $template->body) }}</textarea>
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

@push('styles')
<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
<link href="https://unpkg.com/grapesjs-preset-newsletter/dist/grapesjs-preset-newsletter.min.css" rel="stylesheet">
<style>
    .gjs-one-bg { background-color: #0f172a; }
    .gjs-two-color { color: rgba(255,255,255,0.7); }
    .gjs-three-bg { background-color: #1e293b; color: #fff; }
    .gjs-four-color, .gjs-four-color-h:hover { color: #94a3b8; }
    #gjs { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter"></script>
<script>
$(document).ready(function() {
    let grapesEditor;

    function initGrapesJS(html) {
        if (grapesEditor) { let c = cleanHtml(html || ''); grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); return; }
        grapesEditor = grapesjs.init({
            container: '#gjs',
            height: '500px',
            width: 'auto',
            storageManager: false,
            plugins: ['grapesjs-preset-newsletter'],
            pluginsOpts: {
                'grapesjs-preset-newsletter': {
                    modalLabelImport: 'Paste HTML',
                    cellStyle: { 'font-size':'14px','font-family':'Inter,Arial,sans-serif','color':'#0f172a' }
                }
            },
            canvas: { styles: ['https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'] },
        });
        let c = cleanHtml(html || '');
        if (c.html) { grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); }
        grapesEditor.on('component:update', sync);
        grapesEditor.on('style:update', sync);
    }

    // Strip full HTML doc to body+styles for GrapesJS
    function cleanHtml(raw) {
        let h = raw || '', css = '';
        const sm = h.match(/<style[^>]*>([\s\S]*?)<\/style>/gi);
        if (sm) { css = sm.map(s => s.replace(/<\/?style[^>]*>/gi, '')).join('\n'); h = h.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ''); }
        const bm = h.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
        if (bm) { h = bm[1].trim(); } else { h = h.replace(/<!DOCTYPE[^>]*>/gi, '').replace(/<html[^>]*>|<\/html>/gi, '').replace(/<head[^>]*>[\s\S]*?<\/head>/gi, ''); }
        return { html: h.trim(), css: css };
    }

    function sync() {
        if (!grapesEditor) return;
        const h = grapesEditor.getHtml(), c = grapesEditor.getCss();
        $('#body').val('<style>' + c + '</style>' + h);
    }

    function getContent() {
        if (!grapesEditor) return $('#bodyCode').val() || '';
        return '<style>' + grapesEditor.getCss() + '</style>' + grapesEditor.getHtml();
    }

    function setContent(html) {
        let c = cleanHtml(html || '');
        if (grapesEditor) { grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); }
        $('#bodyCode').val(html);
        $('#body').val(html);
    }

    initGrapesJS($('#body').val() || '');
    sync();
    setInterval(sync, 2000);

    $('input[name="editorMode"]').on('change', function() {
        if ($('#codeMode').is(':checked')) {
            $('#bodyCode').val(getContent());
            $('#visualEditorContainer').addClass('d-none');
            $('#codeEditorContainer').removeClass('d-none');
        } else {
            setContent($('#bodyCode').val());
            $('#codeEditorContainer').addClass('d-none');
            $('#visualEditorContainer').removeClass('d-none');
        }
    });

    $('#bodyCode').on('input', function() { $('#body').val($(this).val()); });

    $('#templateForm').on('submit', function() {
        if ($('#visualMode').is(':checked')) $('#body').val(getContent());
        else $('#body').val($('#bodyCode').val());
    });
});
</script>
@endpush
