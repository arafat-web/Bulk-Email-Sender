<div class="mb-3">
    <label for="key" class="form-label">Keyword <span style="color:#ef4444;">*</span></label>
    <div class="input-group">
        <span class="input-group-text">[</span>
        <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key"
               value="{{ old('key', $keyword->key ?? '') }}" required maxlength="50"
               placeholder="company_name" pattern="[A-Za-z0-9_\- ]+"
               style="font-family:ui-monospace,monospace;">
        <span class="input-group-text">]</span>
        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
        Letters, numbers, spaces, dashes and underscores only — saved lowercase with underscores. Preview: <code id="keyPreview">[{{ old('key', $keyword->key ?? 'company_name') }}]</code>
    </div>
</div>

<div class="mb-3">
    <label for="label" class="form-label">Display Label</label>
    <input type="text" class="form-control" id="label" name="label"
           value="{{ old('label', $keyword->label ?? '') }}" maxlength="255" placeholder="e.g. Company Name">
</div>

<div class="mb-3">
    <label for="source_column" class="form-label">Source Field <span style="color:#ef4444;">*</span></label>
    <select class="form-select" id="source_column" name="source_column">
        <option value="">— Fixed value only (no per-recipient data) —</option>
        <option value="name" {{ old('source_column', $keyword->source_column ?? '') === 'name' ? 'selected' : '' }}>Name (full name, then first name)</option>
        @foreach($sources as $col => $name)
            <option value="{{ $col }}" {{ old('source_column', $keyword->source_column ?? '') === $col ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
        Per-recipient value taken from the uploaded CSV / contact record. For campaigns from contacts the same fields apply.
    </div>
</div>

<div class="mb-3">
    <label for="default_value" class="form-label">Default Value</label>
    <input type="text" class="form-control" id="default_value" name="default_value"
           value="{{ old('default_value', $keyword->default_value ?? '') }}" maxlength="1000" placeholder="Used when a recipient has no value, e.g. Valued Customer">
    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
        Leave empty to render nothing when no data exists.
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Optional note for admins...">{{ old('description', $keyword->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
               {{ old('is_active', $keyword->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active" style="font-size:13px;">Active (replaced in outgoing emails)</label>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('key').addEventListener('input', function() {
    var v = this.value.toLowerCase().trim().replace(/[\s\-]+/g, '_').replace(/[^a-z0-9_]/g, '') || 'keyword';
    document.getElementById('keyPreview').textContent = '[' + v + ']';
});
</script>
@endpush
