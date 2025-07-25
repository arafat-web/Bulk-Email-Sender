@extends('layouts.app')

@section('title', 'Email Contacts - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Contacts</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Email Contacts</h1>
                    <p class="text-muted mb-0">Manage your email contacts and organize them with tags</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Contact
                    </a>
                    <a href="{{ route('contacts.import.form') }}" class="btn btn-outline-primary">
                        <i class="bi bi-upload me-2"></i>Import
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('contacts.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="tag" class="form-select">
                                <option value="">All Tags</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                                <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions Form -->
            <form id="bulkActionForm" method="POST" action="{{ route('contacts.bulk-action') }}">
                @csrf
                <input type="hidden" name="action" id="bulkAction">
                <input type="hidden" name="tag_id" id="bulkTagId">
                <input type="hidden" name="status" id="bulkStatus">

                <!-- Contacts Table -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-people me-2 text-primary"></i>Contacts ({{ $contacts->total() }})
                            </h5>

                            <!-- Bulk Actions -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" disabled>
                                    <i class="bi bi-gear me-2"></i>Bulk Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="setBulkAction('add_tag')">
                                        <i class="bi bi-tag me-2"></i>Add Tag
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setBulkAction('remove_tag')">
                                        <i class="bi bi-tag-fill me-2"></i>Remove Tag
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setBulkAction('change_status')">
                                        <i class="bi bi-toggle-on me-2"></i>Change Status
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="setBulkAction('delete')">
                                        <i class="bi bi-trash me-2"></i>Delete Selected
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($contacts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Contact</th>
                                            <th>Tags</th>
                                            <th>Status</th>
                                            <th>Last Emailed</th>
                                            <th>Created</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contacts as $contact)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="contacts[]" value="{{ $contact->id }}" class="form-check-input contact-checkbox">
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong class="text-white">{{ $contact->full_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $contact->email }}</small>
                                                        @if($contact->company)
                                                            <br>
                                                            <small class="text-info">{{ $contact->company }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @foreach($contact->tags as $tag)
                                                        <span class="badge me-1" style="background-color: {{ $tag->color }};">
                                                            {{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'active' => 'success',
                                                            'inactive' => 'secondary',
                                                            'bounced' => 'danger',
                                                            'unsubscribed' => 'warning'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$contact->status] ?? 'secondary' }}">
                                                        {{ ucfirst($contact->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $contact->last_emailed_at ? $contact->last_emailed_at->diffForHumans() : 'Never' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $contact->created_at->format('M j, Y') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-info btn-sm" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" title="Delete" onclick="deleteContact({{ $contact->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No contacts found</h5>
                                <p class="text-muted">Start by adding your first contact or importing from a file.</p>
                                <div class="mt-3">
                                    <a href="{{ route('contacts.create') }}" class="btn btn-primary me-2">
                                        <i class="bi bi-plus-circle me-2"></i>Add Contact
                                    </a>
                                    <a href="{{ route('contacts.import.form') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-upload me-2"></i>Import Contacts
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($contacts->hasPages())
                        <div class="card-footer bg-transparent border-0">
                            {{ $contacts->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Contact Modal -->
<div class="modal fade" id="deleteContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Delete Contact</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                Are you sure you want to delete this contact? This action cannot be undone.
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteContactForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modals -->
@include('contacts.partials.bulk-modals')

@endsection

@push('styles')
<style>
    .form-control, .form-select {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: var(--primary-color);
        color: #374151;
        box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .card {
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }

    .table th {
        border-color: #e5e7eb;
        font-weight: 600;
        color: #374151;
    }

    .table td {
        border-color: #f3f4f6;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(139, 92, 246, 0.05);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #6d28d9 100%);
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }

    .dropdown-menu {
        border: none;
        box-shadow: var(--shadow-lg);
        border-radius: 12px;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(139, 92, 246, 0.1);
        color: var(--primary-color);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkActionsBtn = document.getElementById('bulkActionsDropdown');

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsState();
    });

    contactCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === contactCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < contactCheckboxes.length;
            updateBulkActionsState();
        });
    });

    function updateBulkActionsState() {
        const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
        bulkActionsBtn.disabled = checkedBoxes.length === 0;
    }
});

function deleteContact(contactId) {
    const form = document.getElementById('deleteContactForm');
    form.action = `{{ route('contacts.index') }}/${contactId}`;
    new bootstrap.Modal(document.getElementById('deleteContactModal')).show();
}

function setBulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one contact.');
        return;
    }

    document.getElementById('bulkAction').value = action;

    if (action === 'delete') {
        if (confirm(`Are you sure you want to delete ${checkedBoxes.length} contact(s)?`)) {
            document.getElementById('bulkActionForm').submit();
        }
    } else if (action === 'add_tag' || action === 'remove_tag') {
        new bootstrap.Modal(document.getElementById('tagModal')).show();
    } else if (action === 'change_status') {
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }
}

function submitBulkTagAction(tagId) {
    document.getElementById('bulkTagId').value = tagId;
    document.getElementById('bulkActionForm').submit();
}

function submitBulkStatusAction(status) {
    document.getElementById('bulkStatus').value = status;
    document.getElementById('bulkActionForm').submit();
}
</script>
@endpush
