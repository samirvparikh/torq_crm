@extends('layouts.crm')

@section('title', 'Dashboard')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a>
    <span>&rsaquo;</span> Dashboard
@endsection

@section('toolbar')
    <div class="crm-page-title">
        <i class="bi bi-speedometer2"></i> Dashboard
    </div>
@endsection

@section('content')
    <div class="crm-stats-grid">
        @foreach ([
            'today_leads' => ['Today Leads', 'bi-calendar-day'],
            'weekly_leads' => ['Weekly Leads', 'bi-calendar-week'],
            'pending_followups' => ['Pending Followups', 'bi-clock-history'],
            'won_leads' => ['Won Leads', 'bi-trophy'],
            'total_leads' => ['Total Leads', 'bi-funnel'],
            'conversion_rate' => ['Conversion Rate', 'bi-graph-up-arrow'],
        ] as $key => [$label, $icon])
            <div class="crm-stat-card">
                <p><i class="bi {{ $icon }}"></i> {{ $label }}</p>
                <h3>
                    @if($key === 'conversion_rate')
                        {{ $stats[$key] ?? 0 }}%
                    @elseif($key === 'revenue')
                        ₹{{ number_format($stats[$key] ?? 0, 0) }}
                    @else
                        {{ number_format($stats[$key] ?? 0) }}
                    @endif
                </h3>
            </div>
        @endforeach
        <div class="crm-stat-card">
            <p><i class="bi bi-currency-rupee"></i> Revenue</p>
            <h3>₹{{ number_format($stats['revenue'] ?? 0, 0) }}</h3>
        </div>
    </div>

    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <p style="color:var(--crm-muted);margin:0;">
                Welcome, <strong>{{ auth()->user()->name }}</strong>
                @if(auth()->user()->primaryRoleName())
                    ({{ auth()->user()->primaryRoleName() }})
                @endif
                — your lead pipeline overview is above.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
setInterval(async () => {
    try {
        const res = await fetch('{{ route('api.dashboard.stats') }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (res.ok) { /* future: refresh stat cards */ }
    } catch (e) {}
}, 30000);
</script>
@endpush
