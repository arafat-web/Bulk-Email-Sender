@extends('layouts.app')

@section('title', 'Create Template - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Templates</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Create Email Template</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Design a reusable template for your campaigns.</p>
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
                <form action="{{ route('email-templates.store') }}" method="POST" id="templateForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Template Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Welcome Email" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="subject" class="form-label">Email Subject <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Subject line" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" placeholder="Brief description (optional)">
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
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">HTML Code <span style="color:#ef4444;">*</span></label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror" rows="18" placeholder="Enter your HTML code..." style="font-size:13px;">{{ old('body') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active" style="font-size:13px;">Active (available for campaigns)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Tips</h5></div>
            <div class="card-body" style="font-size:13px;">
                <ul class="list-unstyled mb-0" style="display:flex;flex-direction:column;gap:8px;">
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Drag blocks from the right panel to build your email</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Click any element to edit text or styles</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Use inline styles for email client compatibility</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Toggle device preview to check mobile layout</li>
                </ul>
            </div>
        </div>

        <a href="https://grapesjs.com/docs/" target="_blank" rel="noopener" style="text-decoration:none;">
            <div style="background:#0f172a;border-radius:10px;padding:16px 18px;display:flex;align-items:center;gap:12px;transition:opacity 0.15s;">
                <div style="width:32px;height:32px;background:rgba(255,255,255,0.1);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#fff;">GrapesJS Documentation</div>
                    <div style="font-size:11px;color:#94a3b8;">Learn all builder features &rarr;</div>
                </div>
            </div>
        </a>
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
