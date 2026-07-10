@extends('layouts.crm')

@section('title', 'Companies')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Companies
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-building"></i> Companies</div>
@endsection

@section('content')
    <div class="crm-content-card"><div class="crm-content-card-body"><p style="color:var(--crm-muted);margin:0;">Company management module.</p></div></div>
@endsection
