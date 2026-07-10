@extends('layouts.crm')

@section('title', 'Customers')
@section('module', 'customers')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Customers
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-people"></i> Customers</div>
    <div class="crm-toolbar-actions">
        @can('create', App\Models\Customer::class)
            <a href="{{ route('customers.create') }}" class="crm-btn crm-btn-primary-sm"><i class="bi bi-plus-lg"></i> Add Customer</a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <p style="color:var(--crm-muted);margin:0;">Customer directory — use the create form to add new customers.</p>
        </div>
    </div>
@endsection
