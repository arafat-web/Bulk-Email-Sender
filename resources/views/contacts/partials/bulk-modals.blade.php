<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($tags as $tag)
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkTagAction({{ $tag->id }})">
                            <span class="badge" style="background-color:{{ $tag->color }};color:#fff;">{{ $tag->name }}</span>
                            <span class="badge bg-light text-dark">{{ $tag->contacts_count ?? 0 }}</span>
                        </button>
                    @endforeach
                    @if($tags->isEmpty())
                        <div class="text-center py-3">
                            <p style="color:#94a3b8;margin-bottom:8px;">No tags available</p>
                            <a href="{{ route('tags.create') }}" class="btn btn-primary btn-sm">Create Tag</a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action" onclick="submitBulkStatusAction('active')">
                        <span class="badge bg-success me-2">Active</span> Contact can receive emails
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="submitBulkStatusAction('inactive')">
                        <span class="badge bg-secondary me-2">Inactive</span> Temporarily disabled
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="submitBulkStatusAction('bounced')">
                        <span class="badge bg-danger me-2">Bounced</span> Email address bounced
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="submitBulkStatusAction('unsubscribed')">
                        <span class="badge bg-warning me-2">Unsubscribed</span> Contact unsubscribed
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
