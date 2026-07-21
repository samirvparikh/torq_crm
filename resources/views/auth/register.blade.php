@extends('layouts.auth')

@section('title', 'Register')
@section('auth_heading', 'Create Account')
@section('auth_subheading', 'Register to access your business workspace.')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="crm-field">
            <label class="crm-field-label" for="username">Username</label>
            <input id="username" type="text" name="username" class="crm-input"
                   value="{{ old('username') }}" required autofocus autocomplete="username">
            @error('username')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" class="crm-input"
                   value="{{ old('first_name') }}" required autocomplete="given-name">
            @error('first_name')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" class="crm-input"
                   value="{{ old('last_name') }}" autocomplete="family-name">
            @error('last_name')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="crm-input"
                   value="{{ old('email') }}" required autocomplete="username">
            @error('email')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="mobile">Mobile</label>
            <input id="mobile" type="text" name="mobile" class="crm-input"
                   value="{{ old('mobile') }}" inputmode="numeric" autocomplete="tel">
            @error('mobile')<div class="crm-auth-error">{{ $message }}</div>@enderror
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
        <button type="submit" class="crm-btn-primary">Register &rarr;</button>
    </form>
    <p style="text-align:center;margin-top:20px;">
        <a class="crm-link" href="{{ route('login') }}">Already registered? Sign In</a>
    </p>
@endsection
