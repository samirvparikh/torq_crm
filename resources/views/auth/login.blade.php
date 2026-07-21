@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    @if (session('status'))
        <div class="crm-security-box" style="margin-bottom:16px;background:#ecfdf5;border-color:#a7f3d0;">
            <i class="bi bi-check-circle" style="color:#16a34a;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="crm-field">
            <label class="crm-field-label" for="login">Email / Username / Mobile</label>
            <input id="login" type="text" name="login" class="crm-input"
                   value="{{ old('login') }}" placeholder="Enter email, username, or mobile"
                   required autofocus autocomplete="username" autocapitalize="none" spellcheck="false">
            @error('login')
                <div class="crm-auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="crm-field">
            <label class="crm-field-label" for="password">Password</label>
            <div class="crm-input-wrap">
                <input id="password" type="password" name="password" class="crm-input"
                       placeholder="Enter Password" required autocomplete="current-password">
                <button type="button" class="crm-toggle-pw" onclick="togglePassword()" aria-label="Toggle password">
                    <i class="bi bi-eye" id="pw-icon"></i>
                </button>
            </div>
            @error('password')
                <div class="crm-auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="crm-field-row" style="margin:20px 0;">
            <label class="crm-checkbox" style="margin:0;">
                <input type="checkbox" name="remember" id="remember_me">
                Remember Me
            </label>
            @if (Route::has('password.request'))
                <a class="crm-link" href="{{ route('password.request') }}">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="crm-btn-primary">Sign In &rarr;</button>
    </form>

    <div class="crm-security-box">
        <i class="bi bi-shield-lock"></i>
        <span>Protected by Enterprise Security with <strong>MFA</strong>, <strong>RBAC</strong> and <strong>Audit Logging</strong>.</span>
    </div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('pw-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush
