@extends('layouts.crm')

@section('title', 'Leads')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a>
    <span>&rsaquo;</span>
    <a href="{{ route('leads.index') }}">Operations</a>
    <span>&rsaquo;</span> Leads
@endsection

@section('toolbar')
    <div class="crm-page-title">
        <i class="bi bi-funnel"></i> Leads
    </div>
    <div class="crm-toolbar-actions">
        @can('create', App\Models\Lead::class)
            <button type="button" class="crm-btn crm-btn-primary-sm" id="lead-sync-btn">
                <i class="bi bi-arrow-repeat"></i> Sync
            </button>
        @endcan
        <button type="button" class="crm-btn" id="lead-refresh-btn"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
        @can('create', App\Models\Lead::class)
            <a href="{{ route('leads.create') }}" class="crm-btn crm-btn-primary-sm"><i class="bi bi-plus-lg"></i> Create Lead</a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <div class="crm-filters">
                <input type="text" id="lead-search" class="crm-input" placeholder="Search leads...">
                <select id="lead-status" class="crm-input">
                    <option value="">All Status</option>
                    @foreach (App\Enums\LeadStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->value }}</option>
                    @endforeach
                </select>
                <select id="lead-source" class="crm-input">
                    <option value="">All Sources</option>
                    @foreach ($leadSources as $source)
                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
                <button type="button" id="lead-filter-btn" class="crm-btn crm-btn-primary-sm"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </div>

        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>Lead #</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="leads-table-body"></tbody>
            </table>
        </div>

        <div class="crm-table-footer">
            <span id="leads-record-info">Showing 0 records</span>
            <div class="crm-pagination" id="leads-pagination"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('leads-table-body');
    const pagination = document.getElementById('leads-pagination');
    const recordInfo = document.getElementById('leads-record-info');
    let currentPage = 1;

    const statusBadge = (status) => {
        const map = { 'Won': 'success', 'Lost': 'danger', 'New': 'info', 'Assigned': 'warning' };
        const cls = map[status] || 'secondary';
        return `<span class="crm-badge crm-badge-${cls}">${status}</span>`;
    };

    async function loadLeads(page = 1) {
        const params = new URLSearchParams({
            page,
            search: document.getElementById('lead-search').value,
            status: document.getElementById('lead-status').value,
            lead_source_id: document.getElementById('lead-source').value,
        });

        const response = await fetch(`{{ route('leads.datatable') }}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await response.json();

        tbody.innerHTML = (json.data || []).map(lead => `
            <tr>
                <td><strong>${lead.lead_number}</strong></td>
                <td>${lead.customer_name}</td>
                <td>${lead.mobile || '-'}</td>
                <td>${lead.lead_source?.name || '-'}</td>
                <td>${statusBadge(lead.status)}</td>
                <td>${lead.assignee?.name || '-'}</td>
                <td><a href="{{ url('leads') }}/${lead.id}" class="crm-link"><i class="bi bi-three-dots"></i></a></td>
            </tr>
        `).join('') || '<tr><td colspan="7" style="text-align:center;color:var(--crm-muted);padding:32px;">No leads found</td></tr>';

        const meta = json.meta;
        const from = meta.total ? ((meta.current_page - 1) * meta.per_page + 1) : 0;
        const to = Math.min(meta.current_page * meta.per_page, meta.total);
        recordInfo.textContent = `Showing ${from}-${to} of ${meta.total} records`;

        pagination.innerHTML = `
            <button ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">&lsaquo;</button>
            <span class="active">${meta.current_page}</span>
            <button ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">&rsaquo;</button>
        `;
        currentPage = meta.current_page;
    }

    document.getElementById('lead-filter-btn').addEventListener('click', () => loadLeads(1));
    document.getElementById('lead-refresh-btn').addEventListener('click', () => loadLeads(currentPage));
    document.getElementById('lead-sync-btn')?.addEventListener('click', async () => {
        const btn = document.getElementById('lead-sync-btn');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Syncing...';
        try {
            const res = await fetch('{{ route('leads.sync-indiamart') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            });
            const json = await res.json();
            alert(json.message || (json.success ? 'Sync complete.' : 'Sync failed.'));
            if (json.success) loadLeads(1);
        } catch (err) {
            alert('Sync failed. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
        }
    });
    pagination.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-page]');
        if (btn) loadLeads(parseInt(btn.dataset.page));
    });

    loadLeads();
});
</script>
@endpush
