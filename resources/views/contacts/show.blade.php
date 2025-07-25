@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-white">{{ $contact->full_name }}</h1>
                    <p class="text-light mb-0">Contact details and activity</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-light">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to Contacts
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-8">
                    <div class="card bg-white/10 backdrop-blur-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0 text-white">Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong class="text-white">Email Address:</strong>
                                    <div class="text-light">{{ $contact->email }}</div>
                                </div>

                                @if($contact->first_name || $contact->last_name)
                                <div class="col-md-6">
                                    <strong class="text-white">Full Name:</strong>
                                    <div class="text-light">{{ trim($contact->first_name . ' ' . $contact->last_name) }}</div>
                                </div>
                                @endif

                                @if($contact->phone)
                                <div class="col-md-6">
                                    <strong class="text-white">Phone:</strong>
                                    <div class="text-light">{{ $contact->phone }}</div>
                                </div>
                                @endif

                                @if($contact->company)
                                <div class="col-md-6">
                                    <strong class="text-white">Company:</strong>
                                    <div class="text-light">{{ $contact->company }}</div>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <strong class="text-white">Status:</strong>
                                    <div>
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
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <strong class="text-white">Last Emailed:</strong>
                                    <div class="text-light">
                                        {{ $contact->last_emailed_at ? $contact->last_emailed_at->format('M j, Y g:i A') : 'Never' }}
                                    </div>
                                </div>

                                @if($contact->notes)
                                <div class="col-12">
                                    <strong class="text-white">Notes:</strong>
                                    <div class="text-light mt-2">{{ $contact->notes }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tags Section -->
                    @if($contact->tags->count() > 0)
                    <div class="card bg-white/10 backdrop-blur-sm border-0">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0 text-white">Tags</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($contact->tags as $tag)
                                    <span class="badge fs-6 px-3 py-2" style="background-color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Activity Sidebar -->
                <div class="col-lg-4">
                    <div class="card bg-white/10 backdrop-blur-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0 text-white">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('individual-emails.create') }}?emails={{ $contact->email }}" class="btn btn-primary">
                                    <i class="bi bi-envelope me-2"></i>Send Email
                                </a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil me-2"></i>Edit Contact
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteContact()">
                                    <i class="bi bi-trash me-2"></i>Delete Contact
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-white/10 backdrop-blur-sm border-0">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0 text-white">Contact Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            <div class="h4 text-primary mb-0">{{ $contact->tags->count() }}</div>
                                            <small class="text-muted">Tags</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            <div class="h4 text-success mb-0">
                                                {{ $contact->created_at->diffInDays() }}
                                            </div>
                                            <small class="text-muted">Days Old</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-secondary">

                            <div class="small text-muted">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Created:</span>
                                    <span>{{ $contact->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Updated:</span>
                                    <span>{{ $contact->updated_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                <p>Are you sure you want to delete <strong>{{ $contact->full_name }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. The contact will be permanently removed from all tags and campaigns.
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Contact</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.875rem;
    }

    .border {
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
</style>
@endpush

@push('scripts')
<script>
function deleteContact() {
    new bootstrap.Modal(document.getElementById('deleteContactModal')).show();
}
</script>
@endpush
