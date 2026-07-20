@extends('layouts.crm')

@section('title', 'Company Profile')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span> Company Profile
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-building-gear"></i> Company Profile</div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h4 style="margin:0 0 14px;font-size:0.95rem;color:var(--crm-muted);letter-spacing:0.04em;text-transform:uppercase;">Company Details</h4>
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Company Name *</label>
                        <input class="crm-input" name="name" value="{{ old('name', $company['name'] ?? '') }}" required @disabled(! $canEdit)>
                        @error('name')<p class="crm-auth-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Branch</label>
                        <input class="crm-input" name="branch" value="{{ old('branch', $company['branch'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Email</label>
                        <input class="crm-input" type="email" name="email" value="{{ old('email', $company['email'] ?? '') }}" @disabled(! $canEdit)>
                        @error('email')<p class="crm-auth-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Phone</label>
                        <input class="crm-input" name="phone" value="{{ old('phone', $company['phone'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Website</label>
                        <input class="crm-input" name="website" value="{{ old('website', $company['website'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">GST Number</label>
                        <input class="crm-input" name="gst_number" value="{{ old('gst_number', $company['gst_number'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Address</label>
                    <textarea class="crm-input" name="address" rows="3" @disabled(! $canEdit)>{{ old('address', $company['address'] ?? '') }}</textarea>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Company Logo</label>
                    <p style="margin:0 0 8px;font-size:12px;color:var(--crm-muted);">Used on quotation / invoice PDF header. JPG, PNG, WEBP — max 2 MB. Click logo to view full size.</p>
                    @if ($logoUrl)
                        <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px;">
                            <button type="button" id="logo-preview-btn" title="View full size" style="padding:0;border:none;background:transparent;cursor:pointer;line-height:0;">
                                <img id="logo-preview-thumb" src="{{ $logoUrl }}" alt="Company logo" style="max-height:72px;max-width:220px;object-fit:contain;border:1px solid var(--crm-border);border-radius:8px;padding:6px;background:#fff;display:block;">
                            </button>
                            @if ($canEdit)
                                <label class="crm-checkbox">
                                    <input type="checkbox" name="remove_logo" value="1"> Remove logo
                                </label>
                            @endif
                        </div>
                    @endif
                    <input class="crm-input" type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" @disabled(! $canEdit)>
                    @error('logo')<p class="crm-auth-error">{{ $message }}</p>@enderror
                </div>

                <h4 style="margin:20px 0 14px;font-size:0.95rem;color:var(--crm-muted);letter-spacing:0.04em;text-transform:uppercase;">Quotation Defaults</h4>
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Signatory Name</label>
                        <input class="crm-input" name="signatory_name" value="{{ old('signatory_name', $company['signatory_name'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Signatory Phone</label>
                        <input class="crm-input" name="signatory_phone" value="{{ old('signatory_phone', $company['signatory_phone'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                </div>
                <div class="crm-field">
                    <label class="crm-field-label">Default Intro Text</label>
                    <textarea class="crm-input" name="intro_text" rows="4" @disabled(! $canEdit)>{{ old('intro_text', $company['intro_text'] ?? '') }}</textarea>
                </div>

                <h4 style="margin:20px 0 14px;font-size:0.95rem;color:var(--crm-muted);letter-spacing:0.04em;text-transform:uppercase;">Bank Details</h4>
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Account Name</label>
                        <input class="crm-input" name="account_name" value="{{ old('account_name', $bank['account_name'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Account Number</label>
                        <input class="crm-input" name="account_number" value="{{ old('account_number', $bank['account_number'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Bank Name</label>
                        <input class="crm-input" name="bank_name" value="{{ old('bank_name', $bank['bank_name'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Branch</label>
                        <input class="crm-input" name="bank_branch" value="{{ old('bank_branch', $bank['branch'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">IFSC</label>
                        <input class="crm-input" name="ifsc" value="{{ old('ifsc', $bank['ifsc'] ?? '') }}" @disabled(! $canEdit)>
                    </div>
                </div>

                @if ($canEdit)
                    <div class="crm-toolbar-actions" style="margin-top:8px;">
                        <button type="submit" class="crm-btn crm-btn-primary-sm">Save Company Profile</button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if ($logoUrl)
        <div class="crm-modal-backdrop" id="logo-modal" style="display:none;">
            <div class="crm-modal" style="max-width:720px;" @click.stop>
                <div class="crm-modal-head">
                    <h3>Company Logo</h3>
                    <button type="button" class="crm-modal-close" id="logo-modal-close">&times;</button>
                </div>
                <div class="crm-modal-body" style="text-align:center;background:#f8fafc;">
                    <img id="logo-modal-img" src="{{ $logoUrl }}" alt="Company logo full size" style="max-width:100%;max-height:70vh;object-fit:contain;">
                </div>
                <div class="crm-modal-foot">
                    <button type="button" class="crm-btn" id="logo-modal-ok">Close</button>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const logoModal = document.getElementById('logo-modal');
    const openLogo = () => { if (logoModal) logoModal.style.display = 'flex'; };
    const closeLogo = () => { if (logoModal) logoModal.style.display = 'none'; };

    document.getElementById('logo-preview-btn')?.addEventListener('click', openLogo);
    document.getElementById('logo-modal-close')?.addEventListener('click', closeLogo);
    document.getElementById('logo-modal-ok')?.addEventListener('click', closeLogo);
    logoModal?.addEventListener('click', (e) => {
        if (e.target === logoModal) closeLogo();
    });

    @if (session('status'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: @json(session('status')),
        confirmButtonColor: '#2563eb',
    });
    @endif
});
</script>
@endpush
