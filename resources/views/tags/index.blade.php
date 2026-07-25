@extends('layouts.app')

@section('title', 'Contact Tags - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Contact Tags</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Contact Tags</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $tags->count() }} tags</p>
    </div>
    <a href="{{ route('tags.create') }}" class="btn btn-primary btn-sm">Create Tag</a>
</div>

<div class="mb-3">
    <input type="text" id="tagSearch" class="form-control" placeholder="Search tags..." style="max-width:320px;">
</div>

@if($tags->count() > 0)
    <div class="row g-3" id="tagsGrid">
        @foreach($tags as $tag)
            <div class="col-md-6 col-lg-4" data-tag-name="{{ strtolower($tag->name) }}">
                <div class="card">
                    <div class="card-body" style="padding:20px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge" style="background-color:{{ $tag->color }};color:#fff;font-size:13px;padding:6px 12px;">{{ $tag->name }}</span>
                            <div class="d-flex gap-1">
                                <a href="{{ route('tags.edit', $tag) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteTag({{ $tag->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        @if($tag->description)
                            <p style="font-size:13px;color:#64748b;margin-bottom:12px;">{{ $tag->description }}</p>
                        @endif
                        <div class="d-flex justify-content-between" style="font-size:12px;color:#94a3b8;">
                            <span>{{ $tag->contacts_count }} contact{{ $tag->contacts_count != 1 ? 's' : '' }}</span>
                            <span>{{ $tag->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <p style="font-size:14px;color:#94a3b8;margin-bottom:16px;">No tags yet.</p>
            <a href="{{ route('tags.create') }}" class="btn btn-primary btn-sm">Create Your First Tag</a>
        </div>
    </div>
@endif

<!-- Delete Modal -->
<div class="modal fade" id="deleteTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this tag?</p>
                <div class="alert alert-warning" style="font-size:13px;">
                    This will remove the tag from all associated contacts. This cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTagForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Tag</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteTagModalInstance = null;
function deleteTag(tagId) {
    document.getElementById('deleteTagForm').action = '{{ route('tags.index') }}/' + tagId;
    if (!deleteTagModalInstance) deleteTagModalInstance = new bootstrap.Modal(document.getElementById('deleteTagModal'));
    deleteTagModalInstance.show();
}

// Client-side tag search
document.getElementById('tagSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#tagsGrid > [data-tag-name]').forEach(function(el) {
        el.style.display = el.dataset.tagName.indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>
@endpush
