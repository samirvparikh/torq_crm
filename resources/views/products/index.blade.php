@extends('layouts.crm')

@section('title', 'Products')
@section('module', 'products')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Products
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-box-seam"></i> Products</div>
@endsection

@section('content')
    <div class="crm-content-card"><div class="crm-content-card-body"><p style="color:var(--crm-muted);margin:0;">Product catalog module.</p></div></div>
@endsection
