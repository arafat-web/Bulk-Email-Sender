@extends('layouts.app')

@section('title', 'Edit Tag - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tags.index') }}">Contact Tags</a></li>
    <li class="breadcrumb-item active">Edit Tag</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-white">Edit Tag</h1>
                    <p class="text-light mb-0">Update tag information and appearance</p>
                </div>
                <a href="{{ route('tags.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back to Tags
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card bg-white/10 backdrop-blur-sm border-0">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0 text-white">Tag Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('tags.update', $tag) }}">
                                @csrf
                                @method('PUT')

                                <!-- Tag Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label text-white">Tag Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $tag->name) }}" required
                                           placeholder="e.g., VIP Customers, Newsletter Subscribers">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tag Color -->
                                <div class="mb-3">
                                    <label for="color" class="form-label text-white">Tag Color <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                               id="color" name="color" value="{{ old('color', $tag->color) }}" required>
                                        <div id="colorPreview" class="badge fs-6" style="background-color: {{ old('color', $tag->color) }};">
                                            {{ old('name', $tag->name) }}
                                        </div>
                                    </div>
                                    <div class="form-text text-muted">Choose a color to represent this tag</div>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preset Colors -->
                                <div class="mb-3">
                                    <label class="form-label text-white">Quick Color Selection</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $presetColors = [
                                                '#6f42c1' => 'Purple',
                                                '#dc3545' => 'Red',
                                                '#fd7e14' => 'Orange',
                                                '#ffc107' => 'Yellow',
                                                '#198754' => 'Green',
                                                '#20c997' => 'Teal',
                                                '#0dcaf0' => 'Cyan',
                                                '#0d6efd' => 'Blue',
                                                '#6610f2' => 'Indigo',
                                                '#d63384' => 'Pink'
                                            ];
                                        @endphp
                                        @foreach($presetColors as $colorValue => $colorName)
                                            <button type="button" class="btn p-2 border {{ $tag->color === $colorValue ? 'border-light' : '' }}"
                                                    style="background-color: {{ $colorValue }}"
                                                    title="{{ $colorName }}"
                                                    onclick="selectColor('{{ $colorValue }}')">
                                                <span class="d-block" style="width: 20px; height: 20px;"></span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label text-white">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Optional description for this tag...">{{ old('description', $tag->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tag Statistics -->
                                <div class="mb-4">
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Tag Usage:</strong> This tag is currently assigned to {{ $tag->contacts()->count() }} contact(s).
                                            </div>
                                            @if($tag->contacts()->count() > 0)
                                                <a href="{{ route('contacts.index', ['tag' => $tag->id]) }}" class="btn btn-sm btn-outline-primary">
                                                    View Contacts
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Tag
                                    </button>
                                </div>
                            </form>
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

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-control-color {
        width: 60px;
        height: 40px;
        border-radius: 0.375rem;
    }

    .badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    .btn[style*="background-color"] {
        transition: transform 0.2s ease;
    }

    .btn[style*="background-color"]:hover {
        transform: scale(1.1);
    }

    .border-light {
        border-width: 2px !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorPreview = document.getElementById('colorPreview');
    const nameInput = document.getElementById('name');

    // Update preview when color changes
    colorInput.addEventListener('input', function() {
        updatePreview();
    });

    // Update preview when name changes
    nameInput.addEventListener('input', function() {
        updatePreview();
    });

    function updatePreview() {
        const color = colorInput.value;
        const name = nameInput.value || 'Preview Tag';
        colorPreview.style.backgroundColor = color;
        colorPreview.textContent = name;
    }
});

function selectColor(color) {
    document.getElementById('color').value = color;
    document.getElementById('color').dispatchEvent(new Event('input'));

    // Update border highlighting
    document.querySelectorAll('.btn[style*="background-color"]').forEach(btn => {
        btn.classList.remove('border-light');
    });
    event.target.closest('.btn').classList.add('border-light');
}
</script>
@endpush
