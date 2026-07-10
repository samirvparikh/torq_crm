@extends('layouts.crm')

@section('title', $customer->name)
@section('module', 'customers')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('customers.index') }}">Customers</a><span>&rsaquo;</span> {{ $customer->name }}
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-person"></i> {{ $customer->name }}</div>
    <a href="{{ route('customers.index') }}" class="crm-btn">Back</a>
@endsection

@section('content')
    <div class="crm-stats-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="crm-stat-card"><p>Mobile</p><h3 style="font-size:1rem;">{{ $customer->mobile ?? '—' }}</h3></div>
        <div class="crm-stat-card"><p>Email</p><h3 style="font-size:1rem;">{{ $customer->email ?? '—' }}</h3></div>
        <div class="crm-stat-card"><p>Company</p><h3 style="font-size:1rem;">{{ $customer->company?->name ?? '—' }}</h3></div>
    </div>
@endsection
