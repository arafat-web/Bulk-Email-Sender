@extends('layouts.app')

@section('title', 'Contact Tags - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Contact Tags</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-white">Contact Tags</h1>
                    <p class="text-light mb-0">Organize your contacts with custom tags</p>
                </div>
                <a href="{{ route('tags.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle me-2"></i>Create Tag
                </a>
            </div>

            <div class="card bg-white/10 backdrop-blur-sm border-0">
                <div class="card-body">
                    @if($tags->count() > 0)
                        <div class="row g-3">
                            @foreach($tags as $tag)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card bg-white/5 border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title mb-2">
                                                        <span class="badge fs-6" style="background-color: {{ $tag->color }};">
                                                            {{ $tag->name }}
                                                        </span>
                                                    </h5>
                                                    @if($tag->description)
                                                        <p class="card-text text-muted small mb-0">{{ $tag->description }}</p>
                                                    @endif
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('tags.edit', $tag) }}">
                                                                <i class="bi bi-pencil me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('contacts.index', ['tag' => $tag->id]) }}">
                                                                <i class="bi bi-people me-2"></i>View Contacts
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-danger" onclick="deleteTag({{ $tag->id }})">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-muted small">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $tag->contacts_count }} contact{{ $tag->contacts_count != 1 ? 's' : '' }}
                                                </div>
                                                <div class="text-muted small">
                                                    Created {{ $tag->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-tags display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No tags created yet</h5>
                            <p class="text-muted">Create tags to organize your email contacts efficiently.</p>
                            <div class="mt-3">
                                <a href="{{ route('tags.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create Your First Tag
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Tag Modal -->
<div class="modal fade" id="deleteTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Delete Tag</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <p>Are you sure you want to delete this tag?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This will remove the tag from all associated contacts. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTagForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Tag</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .badge {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
function deleteTag(tagId) {
    const form = document.getElementById('deleteTagForm');
    form.action = `{{ route('tags.index') }}/${tagId}`;
    new bootstrap.Modal(document.getElementById('deleteTagModal')).show();
}
</script>
@endpush
