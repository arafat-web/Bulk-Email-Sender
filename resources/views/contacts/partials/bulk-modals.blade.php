<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Select Tag</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-white">Choose a tag:</label>
                    <div class="list-group">
                        @foreach($tags as $tag)
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkTagAction({{ $tag->id }})">
                                <div>
                                    <span class="badge me-2" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span>
                                    @if($tag->description)
                                        <small class="text-muted d-block">{{ $tag->description }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-secondary">{{ $tag->contacts_count ?? 0 }} contacts</span>
                            </button>
                        @endforeach

                        @if($tags->isEmpty())
                            <div class="text-center py-3">
                                <p class="text-muted mb-2">No tags available</p>
                                <a href="{{ route('tags.create') }}" class="btn btn-primary btn-sm">Create Tag</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Change Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-white">Select new status:</label>
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkStatusAction('active')">
                            <div>
                                <span class="badge bg-success me-2">Active</span>
                                <span>Contact is active and can receive emails</span>
                            </div>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkStatusAction('inactive')">
                            <div>
                                <span class="badge bg-secondary me-2">Inactive</span>
                                <span>Contact is temporarily inactive</span>
                            </div>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkStatusAction('bounced')">
                            <div>
                                <span class="badge bg-danger me-2">Bounced</span>
                                <span>Email address bounced</span>
                            </div>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="submitBulkStatusAction('unsubscribed')">
                            <div>
                                <span class="badge bg-warning me-2">Unsubscribed</span>
                                <span>Contact has unsubscribed</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
