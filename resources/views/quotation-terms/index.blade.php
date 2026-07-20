@extends('layouts.crm')

@section('title', 'Quotation Terms')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('quotations.index') }}">Quotations</a><span>&rsaquo;</span> Terms Templates
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-card-checklist"></i> Quotation Terms</div>
    <div class="crm-toolbar-actions">
        <button type="button" class="crm-btn crm-btn-primary-sm" id="add-btn"><i class="bi bi-plus-lg"></i> Add Template</button>
    </div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-sort="name" data-dir="asc">Name</th>
                        <th data-sort="is_default" data-dir="desc">Default</th>
                        <th data-sort="is_active" data-dir="desc">Active</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td class="crm-sr">{{ $loop->iteration }}</td>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td>{{ $template->is_default ? 'Yes' : 'No' }}</td>
                            <td>{{ $template->is_active ? 'Yes' : 'No' }}</td>
                            <td style="max-width:360px;white-space:pre-wrap;font-size:11px;color:var(--crm-muted);">{{ \Illuminate\Support\Str::limit($template->content, 160) }}</td>
                            <td class="crm-action-menu">
                                <button type="button" class="edit-btn"
                                    data-id="{{ $template->id }}"
                                    data-name="{{ $template->name }}"
                                    data-content="{{ e($template->content) }}"
                                    data-default="{{ $template->is_default ? 1 : 0 }}"
                                    data-active="{{ $template->is_active ? 1 : 0 }}"
                                    data-sort="{{ $template->sort_order }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="delete-btn" data-id="{{ $template->id }}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--crm-muted);">No templates yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="crm-modal-backdrop" id="modal" style="display:none;">
        <div class="crm-modal">
            <div class="crm-modal-head">
                <h3 id="modal-title">Add Template</h3>
                <button type="button" class="crm-modal-close" id="modal-close">&times;</button>
            </div>
            <form id="term-form">
                <div class="crm-modal-body">
                    <div class="crm-field"><label class="crm-field-label">Name *</label><input class="crm-input" name="name" required></div>
                    <div class="crm-field"><label class="crm-field-label">Content *</label><textarea class="crm-input" name="content" rows="8" required></textarea></div>
                    <div class="crm-field"><label class="crm-checkbox"><input type="checkbox" name="is_default" value="1"> Default template</label></div>
                    <div class="crm-field"><label class="crm-checkbox"><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
                    <p id="form-error" class="crm-auth-error"></p>
                </div>
                <div class="crm-modal-foot">
                    <button type="button" class="crm-btn" id="modal-cancel">Cancel</button>
                    <button type="submit" class="crm-btn crm-btn-primary-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const form = document.getElementById('term-form');
    const tableSort = CrmTable.create({
        sort_by: @json($sort_by ?? 'sort_order'),
        sort_dir: @json($sort_dir ?? 'asc'),
    });
    tableSort.bind(document.querySelector('.crm-table'), () => {
        const params = new URLSearchParams(tableSort.params());
        window.location.search = params.toString();
    });
    let editId = null;

    function openModal(title, data = null) {
        editId = data?.id || null;
        document.getElementById('modal-title').textContent = title;
        form.reset();
        if (data) {
            form.name.value = data.name || '';
            form.content.value = data.content || '';
            form.is_default.checked = !!+data.default;
            form.is_active.checked = data.active === undefined ? true : !!+data.active;
        } else {
            form.is_active.checked = true;
        }
        document.getElementById('form-error').textContent = '';
        modal.style.display = 'flex';
    }
    function closeModal() { modal.style.display = 'none'; editId = null; }

    document.getElementById('add-btn').addEventListener('click', () => openModal('Add Template'));
    ['modal-close','modal-cancel'].forEach(id => document.getElementById(id).addEventListener('click', closeModal));

    document.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', () => openModal('Edit Template', btn.dataset)));
    document.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', async () => {
        if (!confirm('Delete template?')) return;
        await fetch(`{{ url('quotation-terms') }}/${btn.dataset.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        location.reload();
    }));

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const payload = {
            name: form.name.value,
            content: form.content.value,
            is_default: form.is_default.checked,
            is_active: form.is_active.checked,
        };
        const url = editId ? `{{ url('quotation-terms') }}/${editId}` : '{{ route('quotation-terms.store') }}';
        const res = await fetch(url, {
            method: editId ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (json.success) location.reload();
        else document.getElementById('form-error').textContent = json.message || Object.values(json.errors || {}).flat().join(' ');
    });
});
</script>
@endpush
