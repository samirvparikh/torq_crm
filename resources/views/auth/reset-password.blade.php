@extends('layouts.auth')

@section('title', 'Reset Password')
@section('auth_heading', 'Reset Password')
@section('auth_subheading', 'Choose a new password for your account.')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="crm-field">
            <label class="crm-field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="crm-input"
                   value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="crm-input" required autocomplete="new-password">
            @error('password')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="crm-input" required autocomplete="new-password">
            @error('password_confirmation')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="crm-btn-primary">Reset Password &rarr;</button>
    </form>
@endsection
