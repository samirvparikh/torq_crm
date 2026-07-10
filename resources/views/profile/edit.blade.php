@extends('layouts.crm')

@section('title', 'Profile')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Profile Settings
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-gear"></i> Profile Settings</div>
@endsection

@section('content')
    <div class="crm-content-card" style="margin-bottom:16px;">
        <div class="crm-content-card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>
    <div class="crm-content-card" style="margin-bottom:16px;">
        <div class="crm-content-card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
