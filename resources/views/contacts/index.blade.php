@extends('layouts.app')

@section('title', 'Email Contacts - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Contacts</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Email Contacts</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $contacts->total() }} contacts</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm">Add Contact</a>
        <a href="{{ route('contacts.import.form') }}" class="btn btn-outline-primary btn-sm">Import</a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="{{ route('contacts.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="tag" class="form-select">
                    <option value="">All Tags</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary btn-sm">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Contacts Table -->
<form id="bulkActionForm" method="POST" action="{{ route('contacts.bulk-action') }}">
    @csrf
    <input type="hidden" name="action" id="bulkAction">
    <input type="hidden" name="tag_id" id="bulkTagId">
    <input type="hidden" name="status" id="bulkStatus">

    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <h5 class="card-title">Contacts</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span id="selectedCount" style="font-size:12px;color:#64748b;display:none;">0 selected</span>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('add_tag')" id="btnAddTag" disabled>Add Tag</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('remove_tag')" id="btnRemoveTag" disabled>Remove Tag</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('change_status')" id="btnStatus" disabled>Change Status</button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="setBulkAction('delete')" id="btnDelete" disabled>Delete Selected</button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($contacts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Contact</th>
                                <th>Tags</th>
                                <th>Status</th>
                                <th>Last Emailed</th>
                                <th>Created</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td><input type="checkbox" name="contacts[]" value="{{ $contact->id }}" class="form-check-input contact-checkbox"></td>
                                    <td>
                                        <div style="font-weight:600;color:#0f172a;">{{ $contact->full_name }}</div>
                                        <div style="font-size:12px;color:#64748b;">{{ $contact->email }}</div>
                                        @if($contact->company)
                                            <div style="font-size:12px;color:#94a3b8;">{{ $contact->company }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($contact->tags as $tag)
                                            <span class="badge me-1" style="background-color:{{ $tag->color }};color:#fff;">{{ $tag->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success', 'inactive' => 'secondary',
                                                'bounced' => 'danger', 'unsubscribed' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$contact->status] ?? 'secondary' }}">
                                            {{ ucfirst($contact->status) }}
                                        </span>
                                    </td>
                                    <td><span style="font-size:12px;color:#94a3b8;">{{ $contact->last_emailed_at ? $contact->last_emailed_at->diffForHumans() : 'Never' }}</span></td>
                                    <td><span style="font-size:12px;color:#94a3b8;">{{ $contact->created_at->format('M j, Y') }}</span></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteContact({{ $contact->id }})"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p style="font-size:14px;color:#94a3b8;margin-bottom:16px;">No contacts found.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm">Add Contact</a>
                        <a href="{{ route('contacts.import.form') }}" class="btn btn-outline-primary btn-sm">Import</a>
                    </div>
                </div>
            @endif
        </div>
        @if($contacts->hasPages())
            <div class="card-footer bg-transparent">{{ $contacts->withQueryString()->links() }}</div>
        @endif
    </div>
</form>

<!-- Delete Modal -->
<div class="modal fade" id="deleteContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this contact? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteContactForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('contacts.partials.bulk-modals')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');

    function getCheckedCount() {
        return document.querySelectorAll('.contact-checkbox:checked').length;
    }

    function updateBulkState() {
        var n = getCheckedCount();
        var has = n > 0;
        document.getElementById('selectedCount').style.display = has ? 'inline' : 'none';
        document.getElementById('selectedCount').textContent = n + ' selected';
        document.getElementById('btnAddTag').disabled = !has;
        document.getElementById('btnRemoveTag').disabled = !has;
        document.getElementById('btnStatus').disabled = !has;
        document.getElementById('btnDelete').disabled = !has;
    }

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkState();
    });

    contactCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            var checked = getCheckedCount();
            selectAllCheckbox.checked = checked === contactCheckboxes.length;
            selectAllCheckbox.indeterminate = checked > 0 && checked < contactCheckboxes.length;
            updateBulkState();
        });
    });
});

let deleteModalInstance = null;

function deleteContact(contactId) {
    document.getElementById('deleteContactForm').action = '{{ route('contacts.index') }}/' + contactId;
    if (!deleteModalInstance) deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteContactModal'));
    deleteModalInstance.show();
}

function setBulkAction(action) {
    const checked = document.querySelectorAll('.contact-checkbox:checked');
    if (checked.length === 0) { alert('Please select at least one contact.'); return; }
    document.getElementById('bulkAction').value = action;

    if (action === 'delete') {
        if (confirm('Are you sure you want to delete ' + checked.length + ' contact(s)?')) {
            document.getElementById('bulkActionForm').submit();
        }
    } else if (action === 'add_tag' || action === 'remove_tag') {
        (bootstrap.Modal.getInstance(document.getElementById('tagModal')) || new bootstrap.Modal(document.getElementById('tagModal'))).show();
    } else if (action === 'change_status') {
        (bootstrap.Modal.getInstance(document.getElementById('statusModal')) || new bootstrap.Modal(document.getElementById('statusModal'))).show();
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
