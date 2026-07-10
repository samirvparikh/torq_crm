@extends('layouts.auth')

@section('title', 'Verify Email')
@section('auth_heading', 'Verify Email')
@section('auth_subheading', 'Please verify your email address to continue.')

@section('content')
    <p style="color:var(--crm-muted);font-size:0.9rem;margin:0 0 20px;">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="crm-security-box" style="margin-bottom:16px;background:#ecfdf5;border-color:#a7f3d0;">
            <i class="bi bi-check-circle" style="color:#16a34a;"></i>
            <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
        </div>
    @endif

    <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:center;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="crm-btn-primary" style="width:auto;padding:12px 20px;">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="crm-link" style="background:none;border:none;cursor:pointer;">Log Out</button>
        </form>
    </div>
@endsection
