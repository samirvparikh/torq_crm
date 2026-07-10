@extends('layouts.crm')

@section('title', 'Edit Customer')
@section('module', 'customers')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('customers.index') }}">Customers</a><span>&rsaquo;</span> Edit
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-pencil"></i> Edit Customer</div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <div class="crm-form-grid">
                <div class="crm-field">
                    <label class="crm-field-label">Name</label>
                    <input class="crm-input" value="{{ $customer->name }}" readonly>
                </div>
                <div class="crm-field">
                    <label class="crm-field-label">Mobile</label>
                    <input class="crm-input" value="{{ $customer->mobile }}">
                </div>
            </div>
        </div>
    </div>
@endsection
