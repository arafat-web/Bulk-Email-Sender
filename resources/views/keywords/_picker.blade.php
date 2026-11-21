<div class="card mb-3">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
        <h5 class="card-title">Dynamic Keywords</h5>
        <a href="{{ route('keywords.index') }}" style="font-size:12px;color:#64748b;text-decoration:none;">Manage &rarr;</a>
    </div>
    <div class="card-body" style="font-size:13px;">
        @if(isset($keywords) && $keywords->count() > 0)
            <p style="color:#64748b;margin-bottom:10px;">Click to insert at cursor. Replaced per-recipient on send.</p>
            <div class="d-flex flex-wrap gap-1">
                @foreach($keywords as $kw)
                    <button type="button" class="btn btn-sm btn-outline-primary keyword-insert-btn"
                            data-placeholder="[{{ $kw->key }}]"
                            title="{{ $kw->label ?: $kw->key }}{{ $kw->source_column ? ' → '.$kw->source_column : ' → fixed value' }}{{ $kw->default_value ? ' (default: '.$kw->default_value.')' : '' }}">
                        [{{ $kw->key }}]
                    </button>
                @endforeach
            </div>
        @else
            <p style="color:#94a3b8;margin:0;">No keywords configured. <a href="{{ route('keywords.create') }}">Create one</a>.</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Insert keyword placeholder into TinyMCE or plain textarea with id "body" (+ optionally "subject").
(function() {
    function insertAtCursor(text) {
        // Subject field? If focused element is #subject, insert there.
        var active = document.activeElement;
        if (active && active.id === 'subject') {
            var el = active;
            var s = el.selectionStart || el.value.length, e = el.selectionEnd || el.value.length;
            el.value = el.value.slice(0, s) + text + el.value.slice(e);
            el.focus();
            el.selectionStart = el.selectionEnd = s + text.length;
            return;
        }
        if (typeof tinymce !== 'undefined' && tinymce.get('body')) {
            tinymce.get('body').insertContent(text);
            tinymce.get('body').save();
        } else {
            var ta = document.getElementById('body');
            if (!ta) return;
            var s2 = ta.selectionStart || ta.value.length, e2 = ta.selectionEnd || ta.value.length;
            ta.value = ta.value.slice(0, s2) + text + ta.value.slice(e2);
            ta.focus();
            ta.selectionStart = ta.selectionEnd = s2 + text.length;
        }
    }
    document.addEventListener('click', function(ev) {
        var btn = ev.target.closest ? ev.target.closest('.keyword-insert-btn') : null;
        if (!btn) return;
        ev.preventDefault();
        insertAtCursor(btn.getAttribute('data-placeholder'));
    });
})();
</script>
@endpush
