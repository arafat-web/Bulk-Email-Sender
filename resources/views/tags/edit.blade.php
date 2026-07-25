@extends('layouts.app')

@section('title', 'Edit Tag - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tags.index') }}">Tags</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit Tag</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $tag->contacts()->count() }} contact(s) assigned</p>
    </div>
    <a href="{{ route('tags.index') }}" class="btn btn-outline-primary btn-sm">Back to Tags</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Tag Information</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('tags.update', $tag) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tag Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $tag->name) }}" required
                               placeholder="e.g. VIP Customers">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tag Color <span style="color:#ef4444;">*</span></label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                   id="color" name="color" value="{{ old('color', $tag->color) }}" required
                                   style="width:48px;height:38px;padding:2px;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer;">
                            <span id="colorPreview" class="badge" style="background-color:{{ old('color', $tag->color) }};color:#fff;font-size:13px;padding:6px 14px;">{{ old('name', $tag->name) }}</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @php $presetColors = ['#0f172a','#475569','#dc2626','#ea580c','#ca8a04','#16a34a','#0d9488','#2563eb','#7c3aed','#db2777']; @endphp
                            @foreach($presetColors as $c)
                                <button type="button" class="color-swatch" style="width:28px;height:28px;background:{{ $c }};border:2px solid {{ $tag->color === $c ? '#0f172a' : 'transparent' }};border-radius:6px;cursor:pointer;padding:0;" onclick="selectColor('{{ $c }}')" title="{{ $c }}"></button>
                            @endforeach
                        </div>
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Optional description...">{{ old('description', $tag->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tags.index') }}" class="btn btn-outline-primary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Tag</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.color-swatch:hover { border-color: #0f172a !important; transform: scale(1.15); }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('color').addEventListener('input', function() {
    var p = document.getElementById('colorPreview');
    p.style.backgroundColor = this.value;
    p.textContent = document.getElementById('name').value || 'Preview Tag';
});
document.getElementById('name').addEventListener('input', function() {
    document.getElementById('colorPreview').textContent = this.value || 'Preview Tag';
});
function selectColor(c) {
    document.getElementById('color').value = c;
    document.getElementById('color').dispatchEvent(new Event('input'));
    document.querySelectorAll('.color-swatch').forEach(function(s) {
        s.style.borderColor = s.style.background === c ? '#0f172a' : 'transparent';
    });
}
</script>
@endpush
