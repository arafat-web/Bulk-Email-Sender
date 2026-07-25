@extends('layouts.app')

@section('title', 'Individual Emails - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Individual Emails</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Send Individual Emails</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Send personalized emails to specific recipients</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <form id="individualEmailForm" method="POST" action="{{ route('individual-emails.send') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title">Email Configuration</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_account_id" class="form-label">Send From <span style="color:#ef4444;">*</span></label>
                            <select class="form-select @error('email_account_id') is-invalid @enderror" id="email_account_id" name="email_account_id" required>
                                <option value="">Select account</option>
                                @foreach($emailAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('email_account_id') == $account->id ? 'selected' : '' }}>{{ $account->email }} ({{ $account->from_name }})</option>
                                @endforeach
                            </select>
                            @error('email_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="send_type" class="form-label">Sending Type</label>
                            <select class="form-select" id="send_type" name="send_type" required>
                                <option value="individual" {{ old('send_type', 'individual') == 'individual' ? 'selected' : '' }}>Individual (separate per recipient)</option>
                                <option value="bulk" {{ old('send_type') == 'bulk' ? 'selected' : '' }}>Bulk (all in one)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h5 class="card-title">Recipients</h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="validateEmails">Validate Emails</button>
                </div>
                <div class="card-body">
                    @if($contacts->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:13px;font-weight:500;">Select from Contacts</span>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllContacts()">All</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearAllContacts()">Clear</button>
                            </div>
                        </div>
                        <select class="form-select form-select-sm mb-2" id="tagFilter" onchange="filterContactsByTag()">
                            <option value="">All Tags</option>
                            @foreach($tags as $tag)<option value="{{ $tag->id }}">{{ $tag->name }}</option>@endforeach
                        </select>
                        <div class="contact-list" style="max-height:250px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:8px;">
                            @foreach($contacts as $contact)
                                <div class="contact-item" data-tags="{{ $contact->tags->pluck('id')->implode(',') }}" style="padding:5px 8px;">
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;flex-wrap:nowrap;min-width:0;">
                                        <input type="checkbox" class="form-check-input contact-checkbox" value="{{ $contact->email }}" data-name="{{ $contact->full_name }}" style="margin:0;flex-shrink:0;">
                                        <span style="font-weight:500;color:#0f172a;white-space:nowrap;flex-shrink:0;">{{ $contact->full_name }}</span>
                                        <span style="color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:1;min-width:0;">{{ $contact->email }}</span>
                                        <span style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                                            @foreach($contact->tags->take(2) as $tag)
                                                <span class="tag-chip" data-tag-id="{{ $tag->id }}" style="display:inline-flex;align-items:center;gap:2px;padding:1px 6px;border-radius:3px;background:{{ $tag->color }}18;color:{{ $tag->color }};font-size:10px;font-weight:500;cursor:pointer;white-space:nowrap;" onclick="event.stopPropagation();filterContactsByTagId({{ $tag->id }})" title="Filter by {{ $tag->name }}">
                                                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $tag->color }};flex-shrink:0;"></span>{{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if($contact->tags->count() > 2)
                                                <span style="font-size:10px;color:#94a3b8;cursor:pointer;white-space:nowrap;"
                                                      onclick="event.stopPropagation();showContactTags(this)" title="View all tags"
                                                      data-contact-name="{{ e($contact->full_name) }}"
                                                      data-tags='@json($contact->tags->map(fn($t)=>["name"=>$t->name,"color"=>$t->color]))'>+{{ $contact->tags->count() - 2 }}</span>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2" style="display:flex;align-items:center;gap:12px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addSelectedContacts()">Add Selected</button>
                            <span style="font-size:12px;color:#94a3b8;"><span id="selectedContactsCount">0</span> selected</span>
                        </div>
                        <hr style="border-color:#e2e8f0;margin:12px 0;">
                    </div>
                    @endif

                    <label for="recipients" class="form-label">Email Addresses <span style="color:#ef4444;">*</span></label>
                    <textarea class="form-control @error('recipients') is-invalid @enderror" id="recipients" name="recipients" rows="5"
                              placeholder="email1@example.com, email2@example.com&#10;email3@example.com" required>{{ old('recipients', $preSelectedEmails) }}</textarea>
                    @error('recipients')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="emailValidation" class="d-none mt-2" style="font-size:12px;">
                        <span style="color:#16a34a;font-weight:600;" id="validCount">0</span> valid &middot;
                        <span style="color:#ef4444;font-weight:600;" id="invalidCount">0</span> invalid &middot;
                        <span style="font-weight:600;" id="totalCount">0</span> total
                        <div id="invalidEmailsList" class="d-none mt-1"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h5 class="card-title">Email Content</h5>
                    <div class="d-flex gap-2">
                        <div class="btn-group btn-group-sm">
                            <input type="radio" class="btn-check" name="editorMode" id="visualMode" checked autocomplete="off">
                            <label class="btn btn-outline-primary" for="visualMode">Design</label>
                            <input type="radio" class="btn-check" name="editorMode" id="codeMode" autocomplete="off">
                            <label class="btn btn-outline-primary" for="codeMode">HTML</label>
                        </div>
                        @if($templates->count() > 0)
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Templates</button>
                                <ul class="dropdown-menu">
                                    @foreach($templates as $template)
                                        <li><a class="dropdown-item template-option" href="#" data-subject="{{ $template->subject }}" data-body="{{ base64_encode($template->body) }}">{{ $template->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Email subject" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="visualEditorContainer">
                        <label class="form-label">Email Body <span style="color:#ef4444;">*</span></label>
                        <div id="gjs"></div>
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">HTML Code <span style="color:#ef4444;">*</span></label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror" rows="14" style="font-size:13px;">{{ old('body') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-primary" id="resetForm">Reset</button>
                <button type="submit" class="btn btn-primary">Send Emails</button>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Email Accounts</h5></div>
            <div class="card-body" style="font-size:13px;">
                @forelse($emailAccounts as $account)
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:32px;height:32px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-envelope" style="color:#64748b;"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:500;color:#0f172a;">{{ $account->from_name }}</div>
                            <div style="color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $account->email }}</div>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8;margin:0;">No accounts. <a href="{{ route('email-accounts.create') }}">Add one</a>.</p>
                @endforelse
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
    // ── GrapesJS ──
    let grapesEditor;

    function cleanHtml(raw) {
        let h = raw || '', css = '';
        var sm = h.match(/<style[^>]*>([\s\S]*?)<\/style>/gi);
        if (sm) { css = sm.map(function(s) { return s.replace(/<\/?style[^>]*>/gi, ''); }).join('\n'); h = h.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ''); }
        var bm = h.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
        if (bm) { h = bm[1].trim(); } else { h = h.replace(/<!DOCTYPE[^>]*>/gi, '').replace(/<html[^>]*>|<\/html>/gi, '').replace(/<head[^>]*>[\s\S]*?<\/head>/gi, ''); }
        return { html: h.trim(), css: css };
    }

    function initGrapesJS(html) {
        if (grapesEditor) { var c = cleanHtml(html || ''); grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); return; }
        grapesEditor = grapesjs.init({
            container: '#gjs', height: '400px', width: 'auto', storageManager: false,
            plugins: ['grapesjs-preset-newsletter'],
            pluginsOpts: { 'grapesjs-preset-newsletter': { modalLabelImport: 'Paste HTML', cellStyle: { 'font-size':'14px','font-family':'Inter,Arial,sans-serif','color':'#0f172a' } } },
            canvas: { styles: ['https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'] },
        });
        var c = cleanHtml(html || '');
        if (c.html) { grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); }
        grapesEditor.on('component:update', sync); grapesEditor.on('style:update', sync);
    }
    function sync() { if (!grapesEditor) return; $('#body').val('<style>' + grapesEditor.getCss() + '</style>' + grapesEditor.getHtml()); }
    function getContent() { if (!grapesEditor) return $('#bodyCode').val() || ''; return '<style>' + grapesEditor.getCss() + '</style>' + grapesEditor.getHtml(); }
    function setContent(html) { var c = cleanHtml(html || ''); if (grapesEditor) { grapesEditor.setComponents(c.html); if (c.css) grapesEditor.setStyle(c.css); } $('#bodyCode').val(html); $('#body').val(html); }

    initGrapesJS($('#body').val() || '');
    sync(); setInterval(sync, 2000);

    $('input[name="editorMode"]').on('change', function() {
        if ($('#codeMode').is(':checked')) { $('#bodyCode').val(getContent()); $('#visualEditorContainer').addClass('d-none'); $('#codeEditorContainer').removeClass('d-none'); }
        else { setContent($('#bodyCode').val()); $('#codeEditorContainer').addClass('d-none'); $('#visualEditorContainer').removeClass('d-none'); }
    });
    $('#bodyCode').on('input', function() { $('#body').val($(this).val()); });

    function decodeBase64(s) { try { return new TextDecoder('utf-8').decode(new Uint8Array([...atob(s)].map(function(c) { return c.charCodeAt(0); }))); } catch(e) { try { return atob(s); } catch(e2) { return s; } } }

    $('.template-option').on('click', function(e) {
        e.preventDefault();
        $('#subject').val($(this).data('subject'));
        setContent(decodeBase64($(this).data('body')));
        $('#visualMode').prop('checked', true).trigger('change');
    });

    // ── Email validation ──

    $('#validateEmails').on('click', function() {
        var r = $('#recipients').val().trim(); if (!r) return;
        var b = $(this); b.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $.ajax({ url: '{{ route("individual-emails.validate") }}', method: 'POST', data: { recipients: r, _token: '{{ csrf_token() }}' },
            success: function(res) {
                $('#validCount').text(res.valid_count); $('#invalidCount').text(res.invalid_count);
                $('#totalCount').text(res.valid_count + res.invalid_count); $('#emailValidation').removeClass('d-none');
                if (res.invalid_count > 0) { var h = ''; res.invalid_emails.forEach(function(e) { h += '<span class="badge bg-danger me-1 mb-1">' + e + '</span>'; }); $('.invalid-emails-container').html(h); $('#invalidEmailsList').removeClass('d-none'); }
                else $('#invalidEmailsList').addClass('d-none');
            },
            error: function(xhr) { Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Validation failed' }); },
            complete: function() { b.prop('disabled', false).html('Validate Emails'); }
        });
    });

    $('#individualEmailForm').on('submit', function(e) {
        e.preventDefault(); if (typeof tinymce !== 'undefined' && tinymce.get('body')) tinymce.get('body').save();
        var fd = new FormData(this); fd.set('body', getContent());
        Swal.fire({ title: 'Sending...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({ url: $(this).attr('action'), method: 'POST', data: fd, processData: false, contentType: false,
            success: function(res) {
                if (res.success) { Swal.fire({ icon: 'success', title: 'Sent!', text: 'Queued ' + res.summary.total_emails + ' emails.' }).then(() => { $('#individualEmailForm')[0].reset(); setContent(''); }); }
                else Swal.fire({ icon: 'error', title: 'Failed', text: res.message });
            },
            error: function(xhr) { Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred' }); }
        });
    });

    $('#resetForm').on('click', function() {
        Swal.fire({ title: 'Reset?', text: 'Clear all data?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes' }).then(function(r) {
            if (r.isConfirmed) { $('#individualEmailForm')[0].reset(); setContent(''); $('#emailValidation').addClass('d-none'); }
        });
    });

    function uc() { $('#selectedContactsCount').text($('.contact-checkbox:checked').length); }
    window.selectAllContacts = function() { $('.contact-item:visible .contact-checkbox').prop('checked', true); uc(); };
    window.clearAllContacts = function() { $('.contact-checkbox').prop('checked', false); uc(); };
    window.filterContactsByTag = function() {
        var t = $('#tagFilter').val(); $('.contact-item').each(function() { var tags = $(this).data('tags').toString().split(','); if (!t || tags.includes(t)) $(this).show(); else { $(this).hide(); $(this).find('.contact-checkbox').prop('checked', false); } }); uc();
    };
    window.filterContactsByTagId = function(tagId) {
        $('#tagFilter').val(tagId); window.filterContactsByTag();
    };
    window.showContactTags = function(el) {
        var name = el.dataset.contactName;
        var tags = JSON.parse(el.dataset.tags);
        var html = tags.map(function(t) { return '<span style="display:inline-flex;align-items:center;gap:4px;margin:2px;"><span style="width:8px;height:8px;border-radius:50%;background:' + t.color + ';"></span>' + t.name + '</span>'; }).join('');
        Swal.fire({ title: name, html: '<div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;">' + html + '</div>', confirmButtonText: 'Close' });
    };
    window.addSelectedContacts = function() {
        var emails = []; $('.contact-checkbox:checked').each(function() { emails.push($(this).val()); });
        if (!emails.length) return;
        var cur = $('#recipients').val().trim(); $('#recipients').val(cur ? cur + ', ' + emails.join(', ') : emails.join(', '));
        $('.contact-checkbox').prop('checked', false); uc();
        Swal.fire({ icon: 'success', title: 'Added', text: emails.length + ' contact(s) added.', timer: 1200, showConfirmButton: false });
    };

    $('.contact-checkbox').on('change', uc); uc();
});
</script>
@endpush
