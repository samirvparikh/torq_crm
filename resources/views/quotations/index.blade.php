@extends('layouts.crm')

@section('title', 'Quotations')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Quotations
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-file-earmark-text"></i> Quotations</div>
@endsection

@section('content')
    <div class="crm-content-card"><div class="crm-content-card-body"><p style="color:var(--crm-muted);margin:0;">Quotation management module.</p></div></div>
@endsection
