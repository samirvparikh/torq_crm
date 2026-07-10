@extends('layouts.crm')

@section('title', 'Tasks')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Tasks
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-list-check"></i> Tasks</div>
@endsection

@section('content')
    <div class="crm-content-card"><div class="crm-content-card-body"><p style="color:var(--crm-muted);margin:0;">Task management module.</p></div></div>
@endsection
