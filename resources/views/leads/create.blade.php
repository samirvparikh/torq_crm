@extends('layouts.crm')

@section('title', 'Create Lead')
@section('module', 'leads')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('leads.index') }}">Leads</a><span>&rsaquo;</span> Create
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-plus-circle"></i> Create Lead</div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <form id="lead-form">
                @csrf
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Customer Name *</label>
                        <input class="crm-input" name="customer_name" required>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Company Name</label>
                        <input class="crm-input" name="company_name">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Mobile</label>
                        <input class="crm-input" name="mobile">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Email</label>
                        <input class="crm-input" type="email" name="email">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Lead Source</label>
                        <select class="crm-input" name="lead_source_id">
                            <option value="">Select Source</option>
                            @foreach ($leadSources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Assign To</label>
                        <select class="crm-input" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="crm-field">
                    <label class="crm-field-label">Requirement</label>
                    <textarea class="crm-input" name="requirement" rows="3"></textarea>
                </div>
                <div class="crm-toolbar-actions" style="margin-top:8px;">
                    <button type="submit" class="crm-btn crm-btn-primary-sm">Save Lead</button>
                    <a href="{{ route('leads.index') }}" class="crm-btn">Cancel</a>
                </div>
                <p id="form-message" class="crm-auth-error"></p>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('lead-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const res = await fetch('{{ route('leads.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data),
    });
    const json = await res.json();
    if (json.success) window.location.href = '{{ route('leads.index') }}';
    else document.getElementById('form-message').textContent = json.message || 'Failed to create lead.';
});
</script>
@endpush
