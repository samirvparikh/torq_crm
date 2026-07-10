@extends('layouts.crm')

@section('title', 'Create Customer')
@section('module', 'customers')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('customers.index') }}">Customers</a><span>&rsaquo;</span> Create
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-person-plus"></i> Create Customer</div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <form class="crm-form-grid" style="display:block;">
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Name *</label>
                        <input class="crm-input" name="name" required>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Mobile</label>
                        <input class="crm-input" name="mobile">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Email</label>
                        <input class="crm-input" type="email" name="email">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Company</label>
                        <select class="crm-input" name="company_id">
                            <option value="">None</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="crm-toolbar-actions" style="margin-top:16px;">
                    <button type="button" class="crm-btn crm-btn-primary-sm">Save Customer</button>
                    <a href="{{ route('customers.index') }}" class="crm-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
