@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('auth_heading', 'Forgot Password')
@section('auth_subheading', 'Enter your email and we will send you a reset link.')

@section('content')
    @if (session('status'))
        <div class="crm-security-box" style="margin-bottom:16px;background:#ecfdf5;border-color:#a7f3d0;">
            <i class="bi bi-check-circle" style="color:#16a34a;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <p style="color:var(--crm-muted);font-size:0.9rem;margin:0 0 20px;">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="crm-field">
            <label class="crm-field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="crm-input"
                   value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="crm-auth-error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="crm-btn-primary">Email Password Reset Link</button>
    </form>

    <p style="text-align:center;margin-top:20px;">
        <a class="crm-link" href="{{ route('login') }}">Back to Sign In</a>
    </p>
@endsection
