<section class="crm-profile-section">
    <header style="margin-bottom:20px;">
        <h2 style="margin:0 0 6px;font-size:1.1rem;font-weight:600;">{{ __('Profile Information') }}</h2>
        <p style="margin:0;color:var(--crm-muted);font-size:0.875rem;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="crm-field">
            <label class="crm-field-label" for="username">{{ __('Username') }}</label>
            <input id="username" name="username" type="text" class="crm-input"
                   value="{{ old('username', $user->username) }}" required autofocus autocomplete="username">
            @error('username')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>

        <div class="crm-form-grid">
            <div class="crm-field">
                <label class="crm-field-label" for="first_name">{{ __('First Name') }}</label>
                <input id="first_name" name="first_name" type="text" class="crm-input"
                       value="{{ old('first_name', $user->first_name) }}" required autocomplete="given-name">
                @error('first_name')<div class="crm-auth-error">{{ $message }}</div>@enderror
            </div>
            <div class="crm-field">
                <label class="crm-field-label" for="last_name">{{ __('Last Name') }}</label>
                <input id="last_name" name="last_name" type="text" class="crm-input"
                       value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name">
                @error('last_name')<div class="crm-auth-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="crm-field">
            <label class="crm-field-label" for="mobile">{{ __('Mobile') }}</label>
            <input id="mobile" name="mobile" type="text" class="crm-input"
                   value="{{ old('mobile', $user->mobile) }}" inputmode="numeric" autocomplete="tel">
            @error('mobile')<div class="crm-auth-error">{{ $message }}</div>@enderror
        </div>

        <div class="crm-field">
            <label class="crm-field-label" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="crm-input"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="crm-auth-error">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p style="margin-top:10px;font-size:0.875rem;color:var(--crm-muted);">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" type="submit" class="crm-link" style="background:none;border:none;cursor:pointer;padding:0;">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p style="margin-top:8px;font-size:0.875rem;color:#16a34a;">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            @endif
        </div>

        <div class="crm-toolbar-actions">
            <button type="submit" class="crm-btn crm-btn-primary-sm">{{ __('Save') }}</button>
            @if (session('status') === 'profile-updated')
                <span style="font-size:0.875rem;color:var(--crm-muted);">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
