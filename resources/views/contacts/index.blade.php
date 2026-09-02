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

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="font-size:13px;"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="font-size:13px;"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger" style="font-size:13px;">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

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
    <input type="hidden" name="bulk_all_filtered" id="bulkAllFiltered" value="0">
    <input type="hidden" name="bulk_search" id="bulkSearch" value="{{ request('search') }}">
    <input type="hidden" name="bulk_tag" id="bulkTag" value="{{ request('tag') }}">
    <input type="hidden" name="bulk_status" id="bulkStatusFilter" value="{{ request('status') }}">

    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div class="d-flex align-items-center gap-3">
                <h5 class="card-title mb-0">Contacts</h5>
                @if($contacts->count() > 0)
                    <div class="dropdown" id="selectDropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="selectDropdownBtn">
                            Select
                        </button>
                        <ul class="dropdown-menu" style="font-size:13px;">
                            <li><button type="button" class="dropdown-item" onclick="selectPage()">Only this page ({{ $contacts->count() }})</button></li>
                            <li><button type="button" class="dropdown-item" onclick="selectAllFiltered()">All filtered ({{ number_format($filteredCount) }})</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button type="button" class="dropdown-item" onclick="clearSelection()">Clear selection</button></li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span id="selectedCount" style="font-size:12px;color:#64748b;display:none;">0 selected</span>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('add_tag')" id="btnAddTag" disabled>Add Tag</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('remove_tag')" id="btnRemoveTag" disabled>Remove Tag</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setBulkAction('change_status')" id="btnStatus" disabled>Change Status</button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="setBulkAction('delete')" id="btnDelete" disabled>Delete Selected</button>
            </div>
        </div>
        <div id="selectAllBanner" class="alert alert-info d-none mb-0 rounded-0 py-2 px-3" style="font-size:12px;border-left:0;border-right:0;border-top:0;">
            <span id="bannerText"></span>
            <button type="button" class="btn btn-sm btn-outline-primary ms-2 py-0 px-2" id="bannerActionBtn" style="font-size:11px;"></button>
            <button type="button" class="btn btn-sm btn-link ms-1 py-0 px-1" onclick="clearSelection()" style="font-size:11px;text-decoration:none;">Clear</button>
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
            <div class="card-footer bg-transparent">
                {{ $contacts->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
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
const FILTERED_COUNT = {{ $filteredCount }};
const FILTER_QUERY = @json(request()->only(['search','tag','status']));

let isAllFiltered = false;

function getVisibleCheckboxes() {
    return document.querySelectorAll('.contact-checkbox');
}

function getVisibleCheckedCount() {
    return document.querySelectorAll('.contact-checkbox:checked').length;
}

function getTotalSelectedCount() {
    return isAllFiltered ? FILTERED_COUNT : getVisibleCheckedCount();
}

function updateBanner() {
    const banner = document.getElementById('selectAllBanner');
    const text = document.getElementById('bannerText');
    const btn = document.getElementById('bannerActionBtn');
    const visible = getVisibleCheckboxes().length;
    const checked = getVisibleCheckedCount();

    if (isAllFiltered) {
        banner.classList.remove('d-none');
        text.textContent = 'All ' + FILTERED_COUNT.toLocaleString() + ' filtered contacts selected.';
        btn.textContent = 'Clear selection';
        btn.onclick = clearSelection;
        if (FILTERED_COUNT > 10000) {
            text.textContent += ' (first 10,000 will be processed)';
        }
    } else if (visible > 0 && checked === visible && FILTERED_COUNT > visible) {
        banner.classList.remove('d-none');
        text.textContent = 'All ' + visible + ' on this page selected.';
        btn.textContent = 'Select all ' + FILTERED_COUNT.toLocaleString() + ' filtered';
        btn.onclick = selectAllFiltered;
    } else {
        banner.classList.add('d-none');
    }
}

function updateBulkState() {
    var n = getTotalSelectedCount();
    var has = n > 0;
    const el = document.getElementById('selectedCount');
    if (el) { el.style.display = has ? 'inline' : 'none'; el.textContent = n + ' selected'; }
    ['btnAddTag','btnRemoveTag','btnStatus','btnDelete'].forEach(id => {
        const b = document.getElementById(id);
        if (b) b.disabled = !has;
    });
    document.getElementById('bulkAllFiltered').value = isAllFiltered ? '1' : '0';
    updateBanner();
}

function selectPage() {
    isAllFiltered = false;
    getVisibleCheckboxes().forEach(cb => cb.checked = true);
    const header = document.getElementById('selectAll');
    if (header) { header.checked = true; header.indeterminate = false; }
    updateBulkState();
}

function clearSelection() {
    isAllFiltered = false;
    getVisibleCheckboxes().forEach(cb => cb.checked = false);
    const header = document.getElementById('selectAll');
    if (header) { header.checked = false; header.indeterminate = false; }
    updateBulkState();
}

async function selectAllFiltered() {
    // Server-side mode: no need to fetch IDs; bulkAction will resolve from filters
    isAllFiltered = true;
    getVisibleCheckboxes().forEach(cb => cb.checked = true);
    const header = document.getElementById('selectAll');
    if (header) { header.checked = true; header.indeterminate = false; }
    updateBulkState();
    // Optionally validate count via ids endpoint (for capped warning)
    try {
        const params = new URLSearchParams(FILTER_QUERY);
        const res = await fetch('{{ route('contacts.ids') }}?' + params.toString(), { headers: { 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            if (data.capped) {
                document.getElementById('bannerText').textContent = 'All ' + data.total.toLocaleString() + ' filtered contacts — first 10,000 will be processed.';
            }
        }
    } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = getVisibleCheckboxes();

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            // "Select all" header checkbox only toggles this page; use dropdown for all-filtered
            contactCheckboxes.forEach(cb => cb.checked = this.checked);
            // If unchecking header while "all filtered" was active, clear extra
            if (!this.checked && extraIds.size > 0) extraIds.clear();
            updateBulkState();
        });
    }

    contactCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            var checked = getVisibleCheckedCount();
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checked === contactCheckboxes.length && contactCheckboxes.length > 0;
                selectAllCheckbox.indeterminate = checked > 0 && checked < contactCheckboxes.length;
            }
            // If user manually unchecks while all-filtered active, drop to page-only mode
            if (extraIds.size > 0 && checked < contactCheckboxes.length) {
                // keep extras but reflect partial; banner stays "all selected" until clear
            }
            updateBulkState();
        });
    });

    updateBulkState();
});

let deleteModalInstance = null;

function deleteContact(contactId) {
    document.getElementById('deleteContactForm').action = '{{ route('contacts.index') }}/' + contactId;
    if (!deleteModalInstance) deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteContactModal'));
    deleteModalInstance.show();
}

function setBulkAction(action) {
    const total = getTotalSelectedCount();
    if (total === 0) { alert('Please select at least one contact.'); return; }
    document.getElementById('bulkAction').value = action;

    if (action === 'delete') {
        if (confirm('Are you sure you want to delete ' + total + ' contact(s)?')) {
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
