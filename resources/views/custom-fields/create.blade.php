@extends('layouts.app')

@section('title', 'Create Custom Field - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('custom-fields.index') }}">Custom Fields</a></li>
    <li class="breadcrumb-item active">Create Custom Field</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Create Custom Field</h1>
                    <p class="text-muted mb-0">Add a new custom field for your contacts</p>
                </div>
                <a href="{{ route('custom-fields.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Custom Fields
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-gear me-2 text-primary"></i>Field Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('custom-fields.store') }}" id="customFieldForm">
                                @csrf

                                <div class="row g-3">
                                    <!-- Field Label -->
                                    <div class="col-12">
                                        <label for="label" class="form-label text-dark fw-medium">Field Label <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('label') is-invalid @enderror"
                                               id="label" name="label" value="{{ old('label') }}" required
                                               placeholder="e.g., Discount Code, Customer Tier">
                                        <div class="form-text">This is the human-readable name that will be displayed in forms.</div>
                                        @error('label')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Field Type -->
                                    <div class="col-md-6">
                                        <label for="type" class="form-label text-dark fw-medium">Field Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">Select field type</option>
                                            @foreach(\App\Models\CustomContactField::$types as $key => $label)
                                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Default Value -->
                                    <div class="col-md-6">
                                        <label for="default_value" class="form-label text-dark fw-medium">Default Value</label>
                                        <input type="text" class="form-control @error('default_value') is-invalid @enderror"
                                               id="default_value" name="default_value" value="{{ old('default_value') }}"
                                               placeholder="Optional default value">
                                        @error('default_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label for="description" class="form-label text-dark fw-medium">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="3" 
                                                  placeholder="Optional description to help users understand this field...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Options for Select Type -->
                                    <div class="col-12" id="options-section" style="display: none;">
                                        <label class="form-label text-dark fw-medium">Options <span class="text-danger">*</span></label>
                                        <div id="options-container">
                                            <div class="option-row mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="options[]" placeholder="Option value">
                                                    <button type="button" class="btn btn-outline-danger remove-option" disabled>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-option">
                                            <i class="bi bi-plus-circle me-1"></i>Add Option
                                        </button>
                                    </div>

                                    <!-- Field Settings -->
                                    <div class="col-12">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">Field Settings</h6>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="is_required" 
                                                                   name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_required">
                                                                Required Field
                                                            </label>
                                                            <div class="form-text">Users must fill this field when creating/editing contacts</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="is_active" 
                                                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active">
                                                                Active Field
                                                            </label>
                                                            <div class="form-text">Only active fields are shown in forms</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('custom-fields.index') }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-lg me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Create Custom Field
                                            </button>
                                        </div>
                                    </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Handle field type change
    $('#type').on('change', function() {
        const selectedType = $(this).val();
        const optionsSection = $('#options-section');
        
        if (selectedType === 'select') {
            optionsSection.show();
            // Make options required for select type
            $('#options-container input[name="options[]"]').attr('required', true);
        } else {
            optionsSection.hide();
            // Remove required attribute from options
            $('#options-container input[name="options[]"]').removeAttr('required');
        }
    });

    // Add option functionality
    $('#add-option').on('click', function() {
        const optionsContainer = $('#options-container');
        const newRow = `
            <div class="option-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="options[]" placeholder="Option value" required>
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        optionsContainer.append(newRow);
        updateRemoveButtons();
    });

    // Remove option functionality
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
        updateRemoveButtons();
    });

    // Update remove button states
    function updateRemoveButtons() {
        const optionRows = $('.option-row');
        const removeButtons = $('.remove-option');
        
        if (optionRows.length <= 1) {
            removeButtons.prop('disabled', true);
        } else {
            removeButtons.prop('disabled', false);
        }
    }

    // Initialize form
    $('#type').trigger('change');
    updateRemoveButtons();
});
</script>
@endpush