@extends('layouts.app')

@section('title', 'Campaign #' . $campaign->id . ' - Live Tracker')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaign Tracker</a></li>
    <li class="breadcrumb-item active">#{{ $campaign->id }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">
            <span class="live-dot" id="liveDot"></span> {{ $campaign->subject ?: '(no subject)' }}
        </h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">
            Campaign #{{ $campaign->id }} &middot; {{ $campaign->file_name }} &middot;
            <span id="liveStatus">{!! $campaign->status_badge !!}</span> &middot;
            Queue pending: <strong id="queuePending">–</strong>
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span id="liveUpdated" style="font-size:12px;color:#94a3b8;"></span>
        <button type="button" id="pauseBtn" class="btn btn-outline-primary btn-sm">Pause</button>
        @if($campaign->failed_count > 0)
            <form action="{{ route('campaigns.retry-failed', $campaign) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Re-queue all failed recipients?')">
                @csrf
                <button class="btn btn-warning btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Retry failed (<span id="retryCount">{{ number_format($campaign->failed_count) }}</span>)</button>
            </form>
        @endif
        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary btn-sm">All campaigns</a>
    </div>
</div>

@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<!-- Live counters -->
<div class="card mb-3">
    <div class="card-body" style="padding:18px 20px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong style="font-size:14px;">Live progress</strong>
            <span style="font-size:22px;font-weight:700;color:#0f172a;font-variant-numeric:tabular-nums;"><span id="cProgress">{{ $campaign->progress_percentage }}</span>%</span>
        </div>
        <div class="tracker-progress mb-3">
            <div class="seg-sent" id="barSent" style="width:0%"></div><div class="seg-failed" id="barFailed" style="width:0%"></div><div class="seg-skipped" id="barSkipped" style="width:0%"></div>
        </div>
        <div class="counter-grid">
            <div class="counter-box"><div class="num" id="cTotal">{{ number_format($campaign->total_email_address) }}</div><div class="lbl">Total</div></div>
            <div class="counter-box sent"><div class="num" id="cSent">{{ number_format($campaign->sent_count) }}</div><div class="lbl">Sent ✓</div></div>
            <div class="counter-box failed"><div class="num" id="cFailed">{{ number_format($campaign->failed_count) }}</div><div class="lbl">Failed ✗</div></div>
            <div class="counter-box pending"><div class="num" id="cPending">{{ number_format($campaign->pending_count) }}</div><div class="lbl">Pending ⏳</div></div>
            <div class="counter-box skipped"><div class="num" id="cSkipped">{{ number_format($campaign->skipped_count ?? 0) }}</div><div class="lbl">Skipped ⊘</div></div>
        </div>
        <div id="lastErrorWrap" class="alert alert-danger mt-3 mb-0 py-2 {{ $campaign->last_error ? '' : 'd-none' }}" style="font-size:12px;">
            Last error: <span id="lastError">{{ $campaign->last_error }}</span>
        </div>
        <div id="doneBanner" class="alert alert-success mt-3 mb-0 py-2 d-none" style="font-size:13px;">Campaign finished — polling paused.</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                <h5 class="card-title">Recipients <span class="text-muted" id="recTotal"></span></h5>
                <div class="d-flex gap-2">
                    <select id="fStatus" class="form-select form-select-sm" style="width:auto;">
                        <option value="">All statuses</option>
                        <option value="queued" {{ $status === 'queued' ? 'selected' : '' }}>Queued</option>
                        <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="skipped" {{ $status === 'skipped' ? 'selected' : '' }}>Skipped</option>
                    </select>
                    <input id="fSearch" type="text" class="form-control form-select-sm" style="width:200px;" placeholder="Search email..." value="{{ $search }}">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Email</th><th>Status</th><th>Attempts</th><th>Error / Info</th><th>Updated</th></tr></thead>
                        <tbody id="recBody">
                            <tr><td colspan="5" class="text-center text-muted py-4">Loading live data...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-2">
                    <span style="font-size:12px;color:#94a3b8;" id="pageInfo"></span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="prevPage">‹ Prev</button>
                        <button class="btn btn-outline-primary btn-sm" id="nextPage">Next ›</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Recent failures</h5></div>
            <div class="card-body" id="failList" style="font-size:12px;">
                <p class="text-muted mb-0">No failures so far. 🎉</p>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title">How to read this</h5></div>
            <div class="card-body" style="font-size:12px;color:#64748b;">
                <ul class="mb-0 ps-3" style="display:flex;flex-direction:column;gap:6px;">
                    <li><strong>Pending</strong> = queued, waiting for the queue worker.</li>
                    <li><strong>Skipped</strong> = invalid address or unsubscribed (not an error).</li>
                    <li><strong>Failed</strong> = SMTP/transport error after all retries — use Retry failed.</li>
                    <li>Counters update every 2 seconds while the campaign is active.</li>
                    <li>Make sure <code>php artisan queue:work --queue=emails,default</code> is running.</li>
                </ul>
            </div>
        </div>
    </div>
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
    var campaignId = {{ $campaign->id }};
    var feedUrl = '{{ route('campaigns.feed', $campaign) }}';
    var page = 1, polling = true, timer = null, lastSig = '';

    function esc(s){ return String(s==null?'':s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
    function statusBadge(s){
        var m = {queued:'bg-info',sent:'bg-success',failed:'bg-danger',skipped:'bg-secondary'};
        return '<span class="badge '+(m[s]||'bg-secondary')+'">'+esc(s)+'</span>';
    }
    function fmt(n){ return Number(n||0).toLocaleString(); }

    function render(res){
        var c = res.campaign;
        var sig = [c.sent,c.failed,c.skipped,c.pending,c.status,res.pagination.total,res.queue.pending_jobs].join('|');
        $('#cTotal').text(fmt(c.total)); $('#cSent').text(fmt(c.sent)); $('#cFailed').text(fmt(c.failed));
        $('#cPending').text(fmt(c.pending)); $('#cSkipped').text(fmt(c.skipped));
        $('#cProgress').text(c.progress); $('#retryCount').text(fmt(c.failed));
        $('#queuePending').text(fmt(res.queue.pending_jobs));
        if(c.total>0){
            $('#barSent').css('width',(c.sent/c.total*100)+'%');
            $('#barFailed').css('width',(c.failed/c.total*100)+'%');
            $('#barSkipped').css('width',(c.skipped/c.total*100)+'%');
        }
        var sm = {processing:'bg-warning',queued:'bg-info',completed:'bg-success',failed:'bg-danger'};
        $('#liveStatus').html('<span class="badge '+(sm[c.status]||'bg-secondary')+'">'+esc(c.status)+'</span>');
        if(c.last_error){ $('#lastError').text(c.last_error); $('#lastErrorWrap').removeClass('d-none'); }
        else { $('#lastErrorWrap').addClass('d-none'); }

        if(res.recipients && res.recipients.length){
            $('#recBody').html(res.recipients.map(function(r){
                return '<tr><td style="font-weight:500;">'+esc(r.email)+'</td><td>'+statusBadge(r.status)+'</td>'
                + '<td>'+r.attempts+'</td><td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#64748b;" title="'+esc(r.error||'')+'">'+esc(r.error||(r.sent_at?('sent '+r.sent_at):'—'))+'</td>'
                + '<td class="text-muted">'+esc(r.updated_at||'')+'</td></tr>';
            }).join(''));
        } else {
            $('#recBody').html('<tr><td colspan="5" class="text-center text-muted py-4">No recipients match this filter.</td></tr>');
        }
        $('#pageInfo').text('Page '+res.pagination.current_page+' of '+res.pagination.last_page+' · '+fmt(res.pagination.total)+' recipients');
        $('#recTotal').text('· '+fmt(res.pagination.total));
        page = res.pagination.current_page;

        if(res.recent_failures && res.recent_failures.length){
            $('#failList').html(res.recent_failures.map(function(f){
                return '<div class="mb-2 pb-2" style="border-bottom:1px solid #f1f5f9;"><div style="font-weight:600;color:#0f172a;">'+esc(f.email)+'</div>'
                + '<div class="text-danger">'+esc((f.error||'').substring(0,140))+'</div>'
                + '<div class="text-muted">'+f.attempts+' attempt(s) · '+esc(f.updated_at||'')+'</div></div>';
            }).join(''));
        } else {
            $('#failList').html('<p class="text-muted mb-0">No failures so far. 🎉</p>');
        }

        $('#liveUpdated').text('Updated ' + new Date().toLocaleTimeString());
        lastSig = sig;

        // Only stop polling on explicit terminal status from the server.
        // Counter-derived is_finished can be stale mid-retry (double counts),
        // which is why the page used to freeze at 100% while mail kept sending.
        if(c.status === 'completed' || c.status === 'failed'){
            $('#doneBanner').removeClass('d-none');
            $('#liveDot').addClass('paused');
            stopPolling();
        }
    }

    function poll(){
        if(!polling || document.hidden) return;
        $.ajax({ url: feedUrl, data: { status: $('#fStatus').val(), q: $('#fSearch').val(), page: page },
            success: render,
            error: function(){ $('#liveUpdated').text('Reconnecting...'); }
        });
    }
    function stopPolling(){ polling = false; if(timer) clearInterval(timer); $('#pauseBtn').text('Resume'); $('#liveDot').addClass('paused'); }
    function startPolling(){ polling = true; $('#pauseBtn').text('Pause'); $('#liveDot').removeClass('paused'); $('#doneBanner').addClass('d-none'); poll(); }

    $('#pauseBtn').on('click', function(){ polling ? stopPolling() : startPolling(); });
    $('#fStatus').on('change', function(){ page = 1; poll(); });
    var searchT = null;
    $('#fSearch').on('input', function(){ clearTimeout(searchT); searchT = setTimeout(function(){ page = 1; poll(); }, 400); });
    $('#prevPage').on('click', function(){ if(page>1){ page--; poll(); } });
    $('#nextPage').on('click', function(){ page++; poll(); });

    document.addEventListener('visibilitychange', function(){ if(!document.hidden && polling) poll(); });

    poll();
    timer = setInterval(poll, 2000);
});
</script>
@endpush
