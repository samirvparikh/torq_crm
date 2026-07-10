<section class="crm-profile-section" x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
    <header style="margin-bottom:20px;">
        <h2 style="margin:0 0 6px;font-size:1.1rem;font-weight:600;">{{ __('Delete Account') }}</h2>
        <p style="margin:0;color:var(--crm-muted);font-size:0.875rem;">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="crm-btn crm-btn-danger" @click="open = true">{{ __('Delete Account') }}</button>

    <div x-show="open" x-cloak style="position:fixed;inset:0;z-index:1000;background:rgba(15,23,42,0.45);display:flex;align-items:center;justify-content:center;padding:24px;">
        <div style="background:#fff;border-radius:16px;max-width:480px;width:100%;padding:28px;box-shadow:var(--crm-shadow);">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h3 style="margin:0 0 8px;font-size:1.1rem;">{{ __('Are you sure you want to delete your account?') }}</h3>
                <p style="margin:0 0 20px;color:var(--crm-muted);font-size:0.875rem;">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
                </p>

                <div class="crm-field">
                    <label class="crm-field-label" for="password">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" class="crm-input" placeholder="{{ __('Password') }}">
                    @if ($errors->userDeletion->has('password'))
                        <div class="crm-auth-error">{{ $errors->userDeletion->first('password') }}</div>
                    @endif
                </div>

                <div class="crm-toolbar-actions" style="margin-top:8px;">
                    <button type="button" class="crm-btn" @click="open = false">{{ __('Cancel') }}</button>
                    <button type="submit" class="crm-btn crm-btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
