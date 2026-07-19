@extends('layouts.crm')

@section('title', $lead->lead_number)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('leads.index') }}">Leads</a><span>&rsaquo;</span> {{ $lead->lead_number }}
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-funnel"></i> {{ $lead->customer_name }}</div>
    <div class="crm-toolbar-actions">
        @can('update', $lead)
            <a href="{{ route('leads.edit', $lead) }}" class="crm-btn crm-btn-primary-sm"><i class="bi bi-pencil"></i> Edit</a>
        @endcan
        <a href="{{ route('leads.index') }}" class="crm-btn">Back</a>
    </div>
@endsection

@section('content')
    <div class="crm-stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
        <div class="crm-stat-card"><p>Status</p><h3 style="font-size:1rem;">{{ $lead->status?->value }}</h3></div>
        <div class="crm-stat-card"><p>Priority</p><h3 style="font-size:1rem;">{{ $lead->priority?->value }}</h3></div>
        <div class="crm-stat-card"><p>Assigned</p><h3 style="font-size:1rem;">{{ $lead->assignee?->name ?? '—' }}</h3></div>
        <div class="crm-stat-card"><p>Mobile</p><h3 style="font-size:1rem;">{{ $lead->mobile ?? '—' }}</h3></div>
        <div class="crm-stat-card"><p>Source</p><h3 style="font-size:1rem;">{{ $lead->leadSource?->name ?? '—' }}</h3></div>
    </div>

    @if($lead->requirement)
        <div class="crm-content-card" style="margin-bottom:16px;">
            <div class="crm-content-card-body">
                <h4 style="margin:0 0 8px;font-size:0.9rem;color:var(--crm-muted);">REQUIREMENT</h4>
                <p style="margin:0;">{{ $lead->requirement }}</p>
            </div>
        </div>
    @endif

    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <h4 style="margin:0 0 16px;font-size:0.9rem;color:var(--crm-muted);">ACTIVITY TIMELINE</h4>
            @forelse ($lead->activities as $activity)
                <div style="border-left:3px solid var(--crm-primary);padding-left:16px;margin-bottom:16px;">
                    <strong>{{ $activity->type?->value }}</strong>
                    <p style="margin:4px 0;color:var(--crm-muted);font-size:0.875rem;">{{ $activity->description }}</p>
                    <small style="color:var(--crm-muted-light);">{{ $activity->created_at?->format('d M Y, h:i A') }} — {{ $activity->causer?->name }}</small>
                </div>
            @empty
                <p style="color:var(--crm-muted);">No activities yet.</p>
            @endforelse
        </div>
    </div>
@endsection
