@extends('layouts.app')

@section('title', 'Keyword Config - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Keyword Config</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Keyword Config</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $stats['total'] }} keywords &middot; {{ $stats['active'] }} active &middot; Use like <code>[name]</code>, <code>[company_name]</code> in templates</p>
    </div>
    <a href="{{ route('keywords.create') }}" class="btn btn-primary btn-sm">Create Keyword</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Label</th>
                        <th>Source Field</th>
                        <th>Default Value</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keywords as $keyword)
                    <tr>
                        <td><code style="font-size:13px;font-weight:600;color:#0f172a;">[{{ $keyword->key }}]</code></td>
                        <td>{{ $keyword->label ?: '—' }}</td>
                        <td>
                            @if($keyword->source_column)
                                <span class="badge bg-info">{{ $keyword->source_column }}</span>
                            @else
                                <span style="color:#94a3b8;">fixed value</span>
                            @endif
                        </td>
                        <td style="color:#64748b;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $keyword->default_value ?: '—' }}</td>
                        <td><span class="badge {{ $keyword->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $keyword->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end" style="white-space:nowrap;">
                            <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('keywords.toggle-active', $keyword) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">{{ $keyword->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form action="{{ route('keywords.destroy', $keyword) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete keyword [{{ $keyword->key }}]? Existing templates keep the text, it just will not be replaced.')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5" style="color:#94a3b8;">No keywords yet. <a href="{{ route('keywords.create') }}">Create your first keyword</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
        <h5 class="card-title">Live Preview</h5>
        <span style="font-size:12px;color:#64748b;">Same replacement engine as sending</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <label for="previewText" class="form-label">Template text to test</label>
                <textarea class="form-control" id="previewText" rows="5" style="font-family:ui-monospace,monospace;font-size:13px;">Dear [company_name],
Hi [name] ([phone], [email])!</textarea>
                <div class="mt-2 d-flex flex-wrap gap-1">
                    @foreach($keywords as $kw)
                        <button type="button" class="btn btn-sm btn-outline-primary preview-insert-btn" data-placeholder="[{{ $kw->key }}]">[{{ $kw->key }}]</button>
                    @endforeach
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Sample data</label>
                @if($sampleContacts->count() > 0)
                    <select class="form-select form-select-sm mb-2" id="previewContact">
                        <option value="">— Custom values below —</option>
                        @foreach($sampleContacts as $c)
                            <option value="{{ $c->id }}" data-first="{{ e($c->first_name) }}" data-last="{{ e($c->last_name) }}" data-email="{{ e($c->email) }}" data-company="{{ e($c->company) }}" data-phone="{{ e($c->phone) }}">{{ e($c->email) }}</option>
                        @endforeach
                    </select>
                @else
                    <p style="font-size:12px;color:#94a3b8;">No contacts yet — using custom values.</p>
                @endif
                <div class="row g-2">
                    <div class="col-6"><input class="form-control form-control-sm preview-field" id="pf_first" placeholder="First name" value="John"></div>
                    <div class="col-6"><input class="form-control form-control-sm preview-field" id="pf_last" placeholder="Last name" value="Doe"></div>
                    <div class="col-12"><input class="form-control form-control-sm preview-field" id="pf_email" placeholder="Email" value="john@example.com"></div>
                    <div class="col-12"><input class="form-control form-control-sm preview-field" id="pf_company" placeholder="Company" value="Acme Inc"></div>
                    <div class="col-12"><input class="form-control form-control-sm preview-field" id="pf_phone" placeholder="Phone" value="+1234567890"></div>
                </div>
                <button type="button" class="btn btn-primary btn-sm mt-2 w-100" id="previewRun">Preview</button>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Result</label>
                <div id="previewOutput" class="border rounded p-2" style="min-height:120px;font-size:13px;background:#f8fafc;white-space:pre-wrap;"></div>
                <div id="previewResolved" class="mt-2 d-flex flex-wrap gap-1"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    function fillFromContact() {
        var sel = document.getElementById('previewContact');
        if (!sel || !sel.value) return false;
        var opt = sel.options[sel.selectedIndex];
        document.getElementById('pf_first').value = opt.dataset.first || '';
        document.getElementById('pf_last').value = opt.dataset.last || '';
        document.getElementById('pf_email').value = opt.dataset.email || '';
        document.getElementById('pf_company').value = opt.dataset.company || '';
        document.getElementById('pf_phone').value = opt.dataset.phone || '';
        return true;
    }
    document.querySelectorAll('.preview-insert-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var ta = document.getElementById('previewText');
            var t = btn.getAttribute('data-placeholder');
            var s = ta.selectionStart || ta.value.length, e = ta.selectionEnd || ta.value.length;
            ta.value = ta.value.slice(0, s) + t + ta.value.slice(e);
            ta.focus();
            ta.selectionStart = ta.selectionEnd = s + t.length;
        });
    });
    var contactSel = document.getElementById('previewContact');
    if (contactSel) contactSel.addEventListener('change', function() { if (this.value) fillFromContact(); runPreview(); });
    document.querySelectorAll('.preview-field').forEach(function(el) {
        el.addEventListener('input', function() { if (contactSel) contactSel.value = ''; });
    });
    function runPreview() {
        var out = document.getElementById('previewOutput');
        out.innerHTML = '<span style="color:#94a3b8;">Loading…</span>';
        fetch('{{ route('keywords.preview') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                text: document.getElementById('previewText').value,
                contact_id: (contactSel && contactSel.value) ? contactSel.value : null,
                first_name: document.getElementById('pf_first').value,
                last_name: document.getElementById('pf_last').value,
                email: document.getElementById('pf_email').value,
                company: document.getElementById('pf_company').value,
                phone: document.getElementById('pf_phone').value
            })
        }).then(function(r) { return r.json(); }).then(function(res) {
            out.textContent = res.output || '';
            var wrap = document.getElementById('previewResolved');
            wrap.innerHTML = '';
            (res.resolved || []).forEach(function(k) {
                var s = document.createElement('span');
                s.className = 'badge bg-light text-dark border';
                s.style.fontSize = '11px';
                s.textContent = k.placeholder + ' → ' + (k.value || '(empty)');
                wrap.appendChild(s);
            });
        }).catch(function() { out.innerHTML = '<span style="color:#dc2626;">Preview failed.</span>'; });
    }
    document.getElementById('previewRun').addEventListener('click', runPreview);
    runPreview();
})();
</script>
@endpush

<div class="card mt-3">
    <div class="card-header"><h5 class="card-title">How it works</h5></div>
    <div class="card-body" style="font-size:13px;color:#475569;">
        <ul class="mb-0 ps-3" style="display:flex;flex-direction:column;gap:6px;">
            <li>Write any keyword in <strong>subject or body</strong> wrapped in square brackets, e.g. <code>Dear [company_name]</code>.</li>
            <li>Each keyword maps to a <strong>contact field</strong> (per-recipient personalization) or a <strong>fixed default value</strong>.</li>
            <li>If a recipient has no value for the mapped field, the <strong>default value</strong> is used instead.</li>
            <li>Inactive keywords are left as-is and never replaced. Unknown <code>[brackets]</code> are also left untouched.</li>
        </ul>
    </div>
</div>
@endsection
