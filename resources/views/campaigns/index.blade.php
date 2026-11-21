@extends('layouts.app')

@section('title', 'Campaign Tracker - BulkMailer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Campaign Tracker</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">
            <span class="live-dot" id="trackerDot"></span> Campaign Tracker
        </h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Realtime send progress — auto-refreshes every 3 seconds.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span id="trackerUpdated" style="font-size:12px;color:#94a3b8;"></span>
        <a href="{{ route('campaigns.failures') }}" class="btn btn-outline-primary btn-sm" id="queueFailuresBtn">
            Failed jobs <span class="badge bg-danger" id="queueFailuresCount">{{ number_format($queueHealth['failed_jobs'] ?? 0) }}</span>
        </a>
        <a href="{{ route('instant.campaign.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Campaign</a>
    </div>
</div>

@if(($queueHealth['failed_jobs'] ?? 0) > 0)
<div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center" style="gap:10px;">
    <div style="font-size:13px;">
        <strong>{{ number_format($queueHealth['failed_jobs']) }} failed job(s)</strong> sitting in the queue
        ({{ number_format($queueHealth['pending_jobs'] ?? 0) }} pending).
        @if(!empty($queueHealth['recent_errors'][0]['error']))
            <br><code style="font-size:11px;">{{ \Str::limit($queueHealth['recent_errors'][0]['error'], 160) }}</code>
        @endif
        <br><span class="text-muted">These were sent before the tracker existed (or orphaned) — that's why the list below is empty.</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('campaigns.failures') }}" class="btn btn-danger btn-sm">View failed emails</a>
        <form action="{{ route('campaigns.failures.sync') }}" method="POST" class="d-inline">@csrf
            <button class="btn btn-outline-primary btn-sm">Sync into tracker</button>
        </form>
    </div>
</div>
@endif

<div class="card mb-3">
    <div class="card-body" style="padding:14px 18px;">
        <form id="trackerFilter" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label" for="q">Search</label>
                <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Subject or file name...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach(['processing','queued','completed','failed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div id="campaignList" class="d-flex flex-column" style="gap:12px;">
    @forelse($campaigns as $campaign)
        @include('campaigns._card', ['campaign' => $campaign])
    @empty
        <div class="card"><div class="card-body text-center py-5">
            <p class="text-muted mb-2">No campaigns yet.</p>
            <a href="{{ route('instant.campaign.create') }}" class="btn btn-primary btn-sm">Start your first campaign</a>
        </div></div>
    @endforelse
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $campaigns->links() }}
</div>
@endsection

@push('styles')
<style>
    .live-dot { display:inline-block; width:10px; height:10px; border-radius:50%; background:#16a34a; margin-right:6px; animation:pulse 1.6s infinite; vertical-align:middle; }
    @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(22,163,74,.5);} 70%{box-shadow:0 0 0 8px rgba(22,163,74,0);} 100%{box-shadow:0 0 0 0 rgba(22,163,74,0);} }
    .live-dot.paused { background:#94a3b8; animation:none; }
    .counter-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:8px; }
    @media(max-width:768px){ .counter-grid{ grid-template-columns:repeat(3,1fr);} }
    .counter-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; text-align:center; }
    .counter-box .num { font-size:18px; font-weight:700; color:#0f172a; font-variant-numeric:tabular-nums; }
    .counter-box .lbl { font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#64748b; font-weight:600; }
    .counter-box.sent .num{color:#16a34a;} .counter-box.failed .num{color:#dc2626;}
    .counter-box.pending .num{color:#d97706;} .counter-box.skipped .num{color:#64748b;}
    .tracker-progress { height:10px; background:#e2e8f0; border-radius:99px; overflow:hidden; }
    .tracker-progress .seg-sent{ background:#16a34a; height:100%; float:left; transition:width .5s; }
    .tracker-progress .seg-failed{ background:#dc2626; height:100%; float:left; transition:width .5s; }
    .tracker-progress .seg-skipped{ background:#94a3b8; height:100%; float:left; transition:width .5s; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var pollTimer = null;
    var polling = true;

    function badge(status){
        var map = {processing:'bg-warning',queued:'bg-info',completed:'bg-success',failed:'bg-danger'};
        return '<span class="badge '+(map[status]||'bg-secondary')+'">'+status+'</span>';
    }
    function esc(s){ return String(s==null?'':s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

    function cardHtml(c){
        var pct = function(v){ return c.total>0 ? (v/c.total*100) : 0; };
        return '<div class="card" data-campaign="'+c.id+'">'
        + '<div class="card-body" style="padding:18px 20px;">'
        + '<div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:10px;">'
        + '<div style="min-width:0;"><div style="font-weight:600;color:#0f172a;font-size:14px;">'+esc(c.subject||'(no subject)')+' <span class="text-muted" style="font-weight:400;">#'+c.id+'</span></div>'
        + '<div style="font-size:12px;color:#94a3b8;">'+esc(c.file_name||c.type||'')+' &middot; '+esc(c.created_at||'')+'</div></div>'
        + '<div class="d-flex align-items-center gap-2">'+badge(c.status)+'<span style="font-size:12px;font-weight:600;color:#0f172a;">'+c.progress+'%</span>'
        + '<a href="'+c.show_url+'" class="btn btn-outline-primary btn-sm">Live view</a></div></div>'
        + '<div class="tracker-progress mt-3"><div class="seg-sent" style="width:'+pct(c.sent)+'%"></div><div class="seg-failed" style="width:'+pct(c.failed)+'%"></div><div class="seg-skipped" style="width:'+pct(c.skipped)+'%"></div></div>'
        + '<div class="counter-grid mt-3">'
        + '<div class="counter-box"><div class="num">'+c.total.toLocaleString()+'</div><div class="lbl">Total</div></div>'
        + '<div class="counter-box sent"><div class="num" data-k="sent">'+c.sent.toLocaleString()+'</div><div class="lbl">Sent</div></div>'
        + '<div class="counter-box failed"><div class="num" data-k="failed">'+c.failed.toLocaleString()+'</div><div class="lbl">Failed</div></div>'
        + '<div class="counter-box pending"><div class="num" data-k="pending">'+c.pending.toLocaleString()+'</div><div class="lbl">Pending</div></div>'
        + '<div class="counter-box skipped"><div class="num" data-k="skipped">'+c.skipped.toLocaleString()+'</div><div class="lbl">Skipped</div></div>'
        + '</div>'
        + '<div style="font-size:12px;color:#64748b;" class="mt-2">'+Number(c.sends_per_hour||0).toLocaleString()+'/hr'
        + (c.eta_at ? ' &middot; ETA '+esc(c.eta_at) : (c.pending>0 ? ' &middot; ETA —' : '')) + '</div>'
        + (c.last_error ? '<div class="alert alert-danger mt-3 mb-0 py-2" style="font-size:12px;">Last error: '+esc(c.last_error)+'</div>' : '')
        + '</div></div>';
    }

    function refreshList(){
        if(!polling || document.hidden) return;
        $.ajax({
            url: '{{ route('campaigns.index') }}',
            data: { q: $('#q').val(), status: $('#status').val() },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res){
                if(!res.campaigns) return;
                var html = res.campaigns.length ? res.campaigns.map(cardHtml).join('')
                    : '<div class="card"><div class="card-body text-center py-4 text-muted">No campaigns match.</div></div>';
                $('#campaignList').html(html);
                $('#trackerUpdated').text('Updated ' + new Date().toLocaleTimeString());
                if(res.queue && typeof res.queue.failed_jobs !== 'undefined'){
                    $('#queueFailuresCount').text(Number(res.queue.failed_jobs).toLocaleString());
                }
                var anyActive = res.campaigns.some(function(c){ return c.status !== 'completed' && c.status !== 'failed'; });
                $('#trackerDot').toggleClass('paused', !anyActive);
                if(!anyActive){ stopPolling(); }
            }
        });
    }

    function stopPolling(){ polling = false; if(pollTimer) clearInterval(pollTimer); $('#trackerDot').addClass('paused'); }

    $('#trackerFilter').on('submit', function(e){
        e.preventDefault();
        polling = true; $('#trackerDot').removeClass('paused');
        refreshList();
    });

    // Poll every 3s while any campaign is active.
    @if($campaigns->count() && $campaigns->contains(fn($c) => !$c->is_finished))
        pollTimer = setInterval(refreshList, 3000);
        setTimeout(refreshList, 1500);
    @else
        $('#trackerDot').addClass('paused');
    @endif
    document.addEventListener('visibilitychange', function(){ if(!document.hidden && polling) refreshList(); });
});
</script>
@endpush
