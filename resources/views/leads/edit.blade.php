@extends('layouts.crm')

@section('title', 'Edit Lead')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('leads.index') }}">Leads</a><span>&rsaquo;</span> Edit
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-pencil"></i> Edit {{ $lead->lead_number }}</div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <form id="lead-edit-form">
                @csrf @method('PUT')
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Customer Name *</label>
                        <input class="crm-input" name="customer_name" value="{{ $lead->customer_name }}" required>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Status</label>
                        <select class="crm-input" name="status">
                            @foreach (App\Enums\LeadStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($lead->status === $status)>{{ $status->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Mobile</label>
                        <input class="crm-input" name="mobile" value="{{ $lead->mobile }}">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Assign To</label>
                        <select class="crm-input" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected($lead->assigned_to == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="crm-toolbar-actions" style="margin-top:8px;">
                    <button type="submit" class="crm-btn crm-btn-primary-sm">Update Lead</button>
                    <a href="{{ route('leads.show', $lead) }}" class="crm-btn">Cancel</a>
                </div>
                <p id="form-message" class="crm-auth-error"></p>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('lead-edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const res = await fetch('{{ route('leads.update', $lead) }}', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data),
    });
    const json = await res.json();
    if (json.success) window.location.href = '{{ route('leads.show', $lead) }}';
    else document.getElementById('form-message').textContent = json.message || 'Update failed.';
});
</script>
@endpush
