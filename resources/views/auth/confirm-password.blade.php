@extends('layouts.auth')

@section('title', 'Confirm Password')
@section('auth_heading', 'Confirm Password')
@section('auth_subheading', 'Please confirm your password before continuing.')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="crm-field">
            <label class="crm-field-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="crm-input"
                   required autocomplete="current-password">
            @error('password')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="crm-btn-primary">Confirm &rarr;</button>
    </form>
@endsection
