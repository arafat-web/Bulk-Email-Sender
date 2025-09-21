@extends('layouts.app')

@section('title', 'Custom Fields - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Custom Fields</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Custom Fields</h1>
                    <p class="text-muted mb-0">Manage custom fields for your contacts</p>
                </div>
                <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Custom Field
                </a>
            </div>

            @if($fields->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 text-dark">
                            <i class="bi bi-gear me-2 text-primary"></i>Custom Fields ({{ $fields->total() }})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Field Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fields as $field)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $field->label }}</strong>
                                                    <br><small class="text-muted">{{ $field->name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $field->type_label }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $field->description ?? 'No description' }}</span>
                                            </td>
                                            <td>
                                                @if($field->is_required)
                                                    <span class="badge bg-warning">Required</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Optional</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($field->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $field->sort_order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('custom-fields.edit', $field) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Edit Field">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('custom-fields.destroy', $field) }}" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this custom field? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Field">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($fields->hasPages())
                        <div class="card-footer bg-white border-top">
                            {{ $fields->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-gear display-1 text-muted"></i>
                        </div>
                        <h5 class="text-dark mb-2">No Custom Fields Yet</h5>
                        <p class="text-muted mb-4">
                            Create custom fields to collect additional information about your contacts.<br>
                            These fields can be used in email templates for personalization.
                        </p>
                        <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Custom Field
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .btn-sm {
        --bs-btn-padding-y: 0.25rem;
        --bs-btn-padding-x: 0.5rem;
        --bs-btn-font-size: 0.875rem;
    }
</style>
@endpush