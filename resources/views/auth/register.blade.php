@extends('layouts.auth')

@section('title', 'Register')
@section('auth_heading', 'Create Account')
@section('auth_subheading', 'Register to access your business workspace.')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="crm-field">
            <label class="crm-field-label" for="name">Name</label>
            <input id="name" type="text" name="name" class="crm-input"
                   value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <div class="crm-field">
            <label class="crm-field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="crm-input"
                   value="{{ old('email') }}" required autocomplete="username">
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
        <button type="submit" class="crm-btn-primary">Register &rarr;</button>
    </form>
    <p style="text-align:center;margin-top:20px;">
        <a class="crm-link" href="{{ route('login') }}">Already registered? Sign In</a>
    </p>
@endsection
