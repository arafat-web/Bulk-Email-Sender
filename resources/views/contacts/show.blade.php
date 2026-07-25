@extends('layouts.app')

@section('title', $contact->full_name . ' - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
    <li class="breadcrumb-item active">{{ $contact->full_name }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">{{ $contact->full_name }}</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $contact->email }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        <button class="btn btn-outline-danger btn-sm" onclick="deleteContact()">Delete</button>
        <a href="{{ route('contacts.index') }}" class="btn btn-outline-primary btn-sm">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Contact Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Email</div>
                        <div style="font-size:14px;color:#0f172a;font-weight:500;">{{ $contact->email }}</div>
                    </div>
                    @if($contact->first_name || $contact->last_name)
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Name</div>
                        <div style="font-size:14px;color:#0f172a;font-weight:500;">{{ trim($contact->first_name . ' ' . $contact->last_name) }}</div>
                    </div>
                    @endif
                    @if($contact->phone)
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Phone</div>
                        <div style="font-size:14px;color:#0f172a;font-weight:500;">{{ $contact->phone }}</div>
                    </div>
                    @endif
                    @if($contact->company)
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Company</div>
                        <div style="font-size:14px;color:#0f172a;font-weight:500;">{{ $contact->company }}</div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Status</div>
                        @php $sc = ['active'=>'success','inactive'=>'secondary','bounced'=>'danger','unsubscribed'=>'warning']; @endphp
                        <span class="badge bg-{{ $sc[$contact->status] ?? 'secondary' }}">{{ ucfirst($contact->status) }}</span>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Last Emailed</div>
                        <div style="font-size:14px;color:#0f172a;">{{ $contact->last_emailed_at ? $contact->last_emailed_at->format('M j, Y g:i A') : 'Never' }}</div>
                    </div>
                    @if($contact->notes)
                    <div class="col-12">
                        <div style="font-size:12px;color:#94a3b8;margin-bottom:2px;">Notes</div>
                        <div style="font-size:14px;color:#0f172a;">{{ $contact->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($contact->tags->count() > 0)
        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title">Tags</h5></div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($contact->tags as $tag)
                        <span class="badge" style="background-color:{{ $tag->color }};color:#fff;font-size:13px;padding:6px 14px;">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Actions</h5></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('individual-emails.create') }}?emails={{ $contact->email }}" class="btn btn-primary btn-sm">Send Email</a>
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-primary btn-sm">Edit Contact</a>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteContact()">Delete Contact</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title">Stats</h5></div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="font-size:22px;font-weight:600;color:#0f172a;">{{ $contact->tags->count() }}</div>
                        <div style="font-size:11px;color:#94a3b8;">Tags</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:22px;font-weight:600;color:#0f172a;">{{ $contact->created_at->diffInDays() }}</div>
                        <div style="font-size:11px;color:#94a3b8;">Days Old</div>
                    </div>
                </div>
                <hr style="border-color:#e2e8f0;">
                <div style="font-size:12px;color:#94a3b8;">
                    <div class="d-flex justify-content-between mb-1"><span>Created</span><span>{{ $contact->created_at->format('M j, Y') }}</span></div>
                    <div class="d-flex justify-content-between"><span>Updated</span><span>{{ $contact->updated_at->format('M j, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Delete Contact</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p>Delete <strong>{{ $contact->full_name }}</strong>?</p>
                <div class="alert alert-warning" style="font-size:13px;">This cannot be undone.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteModalInstance = null;
function deleteContact() {
    if (!deleteModalInstance) deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteContactModal'));
    deleteModalInstance.show();
}
</script>
@endpush
