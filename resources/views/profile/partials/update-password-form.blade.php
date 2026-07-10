<section class="crm-profile-section">
    <header style="margin-bottom:20px;">
        <h2 style="margin:0 0 6px;font-size:1.1rem;font-weight:600;">{{ __('Update Password') }}</h2>
        <p style="margin:0;color:var(--crm-muted);font-size:0.875rem;">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="crm-field">
            <label class="crm-field-label" for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="crm-input" autocomplete="current-password">
            @if ($errors->updatePassword->has('current_password'))
                <div class="crm-auth-error">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="crm-field">
            <label class="crm-field-label" for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="crm-input" autocomplete="new-password">
            @if ($errors->updatePassword->has('password'))
                <div class="crm-auth-error">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="crm-field">
            <label class="crm-field-label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="crm-input" autocomplete="new-password">
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="crm-auth-error">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="crm-toolbar-actions">
            <button type="submit" class="crm-btn crm-btn-primary-sm">{{ __('Save') }}</button>
            @if (session('status') === 'password-updated')
                <span style="font-size:0.875rem;color:var(--crm-muted);">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
