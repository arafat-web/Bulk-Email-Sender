@php
    $total = (int) $campaign->total_email_address;
    $sent = (int) $campaign->sent_count;
    $failed = (int) $campaign->failed_count;
    $skipped = (int) ($campaign->skipped_count ?? 0);
    $processed = $sent + $failed + $skipped;
    $pending = max(0, $total - $processed);
    $progress = $total > 0 ? round(($processed / $total) * 100, 1) : 0;
    $pct = fn($v) => $total > 0 ? ($v / $total * 100) : 0;
@endphp
<div class="card" data-campaign="{{ $campaign->id }}">
    <div class="card-body" style="padding:18px 20px;">
        <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:10px;">
            <div style="min-width:0;">
                <div style="font-weight:600;color:#0f172a;font-size:14px;">
                    {{ $campaign->subject ?: '(no subject)' }} <span class="text-muted" style="font-weight:400;">#{{ $campaign->id }}</span>
                </div>
                <div style="font-size:12px;color:#94a3b8;">{{ $campaign->file_name }} &middot; {{ $campaign->created_at?->format('M d, Y H:i') }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                {!! $campaign->status_badge !!}
                <span style="font-size:12px;font-weight:600;color:#0f172a;">{{ $progress }}%</span>
                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-primary btn-sm">Live view</a>
            </div>
        </div>
        @php
            $rate = (int) ($campaign->sends_per_hour ?? 0);
            $etaAt = $campaign->eta_at ?? null;
        @endphp
        <div style="font-size:12px;color:#64748b;" class="mt-2">
            {{ number_format($rate) }}/hr
            @if($etaAt)
                &middot; ETA {{ $etaAt }}
            @elseif($pending > 0)
                &middot; ETA —
            @endif
        </div>
        <div class="tracker-progress mt-3">
            <div class="seg-sent" style="width:{{ $pct($sent) }}%"></div><div class="seg-failed" style="width:{{ $pct($failed) }}%"></div><div class="seg-skipped" style="width:{{ $pct($skipped) }}%"></div>
        </div>
        <div class="counter-grid mt-3">
            <div class="counter-box"><div class="num">{{ number_format($total) }}</div><div class="lbl">Total</div></div>
            <div class="counter-box sent"><div class="num">{{ number_format($sent) }}</div><div class="lbl">Sent</div></div>
            <div class="counter-box failed"><div class="num">{{ number_format($failed) }}</div><div class="lbl">Failed</div></div>
            <div class="counter-box pending"><div class="num">{{ number_format($pending) }}</div><div class="lbl">Pending</div></div>
            <div class="counter-box skipped"><div class="num">{{ number_format($skipped) }}</div><div class="lbl">Skipped</div></div>
        </div>
        @if($campaign->last_error)
            <div class="alert alert-danger mt-3 mb-0 py-2" style="font-size:12px;">Last error: {{ $campaign->last_error }}</div>
        @endif
    </div>
</div>
