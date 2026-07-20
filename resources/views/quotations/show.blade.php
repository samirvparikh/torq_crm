@extends('layouts.crm')

@section('title', $quotation->quotation_number)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('quotations.index') }}">Quotations</a><span>&rsaquo;</span> {{ $quotation->quotation_number }}
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-file-earmark-text"></i> {{ $quotation->quotation_number }}</div>
    <div class="crm-toolbar-actions">
        @php $canEdit = auth()->user()->can('update', $quotation); @endphp
        @if ($canEdit)
            <div class="crm-edit-dropdown" id="edit-dropdown">
                <button type="button" class="crm-btn" id="edit-menu-btn" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-pencil"></i> Edit <i class="bi bi-chevron-down" style="font-size:11px;margin-left:2px;"></i>
                </button>
                <div id="edit-menu" class="crm-edit-dropdown-menu" hidden>
                    <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="details">Quotation Details</button>
                    <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="subject">Subject &amp; Intro</button>
                    @foreach ($quotation->items as $item)
                        <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="item" data-item-id="{{ $item->id }}" title="{{ $item->product_name }}">{{ $item->product_name }}</button>
                    @endforeach
                    <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="items">Price Breakup</button>
                    <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="terms">Terms &amp; Conditions</button>
                    <button type="button" class="crm-edit-dropdown-item crm-section-edit" data-section="notes">Notes</button>
                </div>
            </div>
        @endif
        <a href="{{ route('quotations.preview', $quotation) }}" target="_blank" class="crm-btn"><i class="bi bi-box-arrow-up-right"></i> Open PDF</a>
        <a href="{{ route('quotations.pdf', $quotation) }}" class="crm-btn crm-btn-primary-sm"><i class="bi bi-download"></i> Download PDF</a>
        <a href="{{ route('quotations.index') }}" class="crm-btn">Back</a>
    </div>
@endsection

@section('content')
@php
    $canEdit = auth()->user()->can('update', $quotation);
    $statusValue = $quotation->status?->value ?? 'Draft';
    $statusMap = ['Draft' => 'secondary', 'Sent' => 'info', 'Accepted' => 'success', 'Rejected' => 'danger', 'Expired' => 'warning'];
@endphp

    @include('quotations.partials.document-styles', ['context' => 'screen'])
    <style>
        .crm-edit-dropdown { position: relative; display: inline-flex; }
        .crm-edit-dropdown-menu {
            position: fixed;
            z-index: 4000;
            min-width: 240px;
            max-width: min(360px, calc(100vw - 24px));
            max-height: min(420px, calc(100vh - 100px));
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--crm-border);
            border-radius: 10px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.18);
            padding: 6px 0;
        }
        .crm-edit-dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 9px 14px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 13px;
            color: var(--crm-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .crm-edit-dropdown-item:hover {
            background: var(--crm-input-bg);
            color: var(--crm-primary);
        }
    </style>

    <div class="quotation-preview-shell">
        <div class="quotation-preview-meta">
            <span>Exact PDF preview — header on every page, page numbers in footer</span>
            <span class="crm-badge crm-badge-{{ $statusMap[$statusValue] ?? 'secondary' }}">{{ $statusValue }}</span>
        </div>
        <iframe
            class="quotation-pdf-frame"
            src="{{ route('quotations.preview', $quotation) }}"
            title="Quotation PDF preview"
        ></iframe>
    </div>

    @if ($canEdit)
        <div class="crm-modal-backdrop" id="section-modal" style="display:none;">
            <div class="crm-modal crm-modal-lg">
                <div class="crm-modal-head">
                    <h3 id="section-modal-title">Edit Section</h3>
                    <button type="button" class="crm-modal-close" id="section-modal-close">&times;</button>
                </div>
                <form id="section-form">
                    <div class="crm-modal-body" id="section-modal-body"></div>
                    <div class="crm-modal-foot">
                        <button type="button" class="crm-btn" id="section-modal-cancel">Cancel</button>
                        <button type="submit" class="crm-btn crm-btn-primary-sm">Save</button>
                    </div>
                    <p id="section-form-error" class="crm-auth-error" style="padding:0 14px 10px;"></p>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('edit-menu-btn');
    const editMenu = document.getElementById('edit-menu');

    function placeEditMenu() {
        if (!editBtn || !editMenu) return;
        if (editMenu.parentElement !== document.body) {
            document.body.appendChild(editMenu);
        }
        const rect = editBtn.getBoundingClientRect();
        editMenu.hidden = false;
        const menuWidth = Math.max(240, editMenu.offsetWidth || 240);
        let left = rect.right - menuWidth;
        left = Math.max(12, Math.min(left, window.innerWidth - menuWidth - 12));
        let top = rect.bottom + 6;
        editMenu.style.left = `${left}px`;
        editMenu.style.top = `${top}px`;
        requestAnimationFrame(() => {
            const h = editMenu.offsetHeight;
            if (top + h > window.innerHeight - 12) {
                editMenu.style.top = `${Math.max(12, rect.top - h - 6)}px`;
            }
        });
    }

    function openEditMenu() {
        if (!editMenu || !editBtn) return;
        editBtn.setAttribute('aria-expanded', 'true');
        placeEditMenu();
    }

    function closeEditMenu() {
        if (!editMenu || !editBtn) return;
        editMenu.hidden = true;
        editBtn.setAttribute('aria-expanded', 'false');
    }

    editBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (editMenu?.hidden) openEditMenu();
        else closeEditMenu();
    });

    document.addEventListener('click', (e) => {
        if (!editMenu || editMenu.hidden) return;
        if (e.target.closest('#edit-menu-btn') || e.target.closest('#edit-menu')) return;
        closeEditMenu();
    });

    window.addEventListener('resize', () => {
        if (editMenu && !editMenu.hidden) placeEditMenu();
    });
    window.addEventListener('scroll', () => {
        if (editMenu && !editMenu.hidden) placeEditMenu();
    }, true);

    editMenu?.addEventListener('click', (e) => e.stopPropagation());

    @if ($canEdit)
    const quotation = @json($quotation);
    const customers = @json($customers);
    const products = @json($products);
    const termTemplates = @json($termTemplates);
    const statuses = @json(App\Enums\QuotationStatus::values());
    const modal = document.getElementById('section-modal');
    const body = document.getElementById('section-modal-body');
    const form = document.getElementById('section-form');
    const title = document.getElementById('section-modal-title');
    const err = document.getElementById('section-form-error');
    let currentSection = null;
    let currentItemId = null;

    const esc = (v) => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
    const jsonField = (v) => v ? JSON.stringify(v, null, 2) : '';

    function closeModal() {
        modal.style.display = 'none';
        currentSection = null;
        currentItemId = null;
        err.textContent = '';
        closeEditMenu();
    }

    function openModal(section, itemId = null) {
        currentSection = section;
        currentItemId = itemId;
        err.textContent = '';
        title.textContent = {
            details: 'Edit Quotation Details',
            subject: 'Edit Subject & Intro',
            items: 'Edit Price Breakup',
            terms: 'Edit Terms & Conditions',
            notes: 'Edit Notes',
            item: 'Edit Machine Details',
        }[section] || 'Edit Section';

        body.innerHTML = renderForm(section, itemId);
        modal.style.display = 'flex';
        closeEditMenu();

        if (section === 'items') bindItemsUi();
        if (section === 'terms') {
            const sel = body.querySelector('[name="quotation_term_template_id"]');
            sel?.addEventListener('change', () => {
                const opt = sel.selectedOptions[0];
                if (opt?.dataset.content) body.querySelector('[name="terms"]').value = opt.dataset.content;
            });
        }
    }

    function renderForm(section, itemId) {
        if (section === 'details') {
            return `
                <div class="crm-form-grid">
                    <div class="crm-field"><label class="crm-field-label">Customer</label>
                        <select class="crm-input" name="customer_id">
                            <option value="">Select Customer</option>
                            ${customers.map(c => `<option value="${c.id}" ${quotation.customer_id == c.id ? 'selected' : ''}>${esc(c.name)}</option>`).join('')}
                        </select>
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Status</label>
                        <select class="crm-input" name="status">
                            ${statuses.map(s => `<option value="${s}" ${(quotation.status?.value || quotation.status) === s ? 'selected' : ''}>${s}</option>`).join('')}
                        </select>
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Quotation Date *</label>
                        <input class="crm-input" type="date" name="quotation_date" value="${(quotation.quotation_date || '').substring(0,10)}" required>
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Valid Until</label>
                        <input class="crm-input" type="date" name="valid_until" value="${(quotation.valid_until || '').substring(0,10)}">
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Tax Type</label>
                        <select class="crm-input" name="tax_type">
                            <option value="igst" ${quotation.tax_type === 'igst' ? 'selected' : ''}>IGST 18%</option>
                            <option value="cgst_sgst" ${quotation.tax_type === 'cgst_sgst' ? 'selected' : ''}>CGST 9% + SGST 9%</option>
                        </select>
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Signatory Name</label>
                        <input class="crm-input" name="signatory_name" value="${esc(quotation.signatory_name)}">
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Signatory Phone</label>
                        <input class="crm-input" name="signatory_phone" value="${esc(quotation.signatory_phone)}">
                    </div>
                </div>`;
        }
        if (section === 'subject') {
            return `
                <div class="crm-field"><label class="crm-field-label">Subject</label>
                    <input class="crm-input" name="subject" value="${esc(quotation.subject)}">
                </div>
                <div class="crm-field"><label class="crm-field-label">Intro Text</label>
                    <textarea class="crm-input" name="intro_text" rows="5">${esc(quotation.intro_text)}</textarea>
                </div>`;
        }
        if (section === 'terms') {
            return `
                <div class="crm-field"><label class="crm-field-label">Template</label>
                    <select class="crm-input" name="quotation_term_template_id">
                        <option value="">Custom</option>
                        ${termTemplates.map(t => `<option value="${t.id}" data-content="${esc(t.content)}" ${quotation.quotation_term_template_id == t.id ? 'selected' : ''}>${esc(t.name)}</option>`).join('')}
                    </select>
                </div>
                <div class="crm-field"><label class="crm-field-label">Terms &amp; Conditions</label>
                    <textarea class="crm-input" name="terms" rows="8">${esc(quotation.terms)}</textarea>
                </div>`;
        }
        if (section === 'notes') {
            return `<div class="crm-field"><label class="crm-field-label">Notes</label>
                <textarea class="crm-input" name="notes" rows="5">${esc(quotation.notes)}</textarea></div>`;
        }
        if (section === 'items') {
            return `
                <div class="crm-field">
                    <div class="crm-table-wrap">
                        <table class="crm-line-items-table">
                            <thead><tr><th>Product</th><th>Capacity</th><th>Qty</th><th>Price</th><th>Tax %</th><th></th></tr></thead>
                            <tbody id="edit-items-body"></tbody>
                        </table>
                    </div>
                    <button type="button" class="crm-btn" id="edit-add-item" style="margin-top:8px;"><i class="bi bi-plus"></i> Add Item</button>
                </div>
                <div class="crm-field"><label class="crm-field-label">Tax Type</label>
                    <select class="crm-input" name="tax_type">
                        <option value="igst" ${quotation.tax_type === 'igst' ? 'selected' : ''}>IGST 18%</option>
                        <option value="cgst_sgst" ${quotation.tax_type === 'cgst_sgst' ? 'selected' : ''}>CGST 9% + SGST 9%</option>
                    </select>
                </div>`;
        }
        if (section === 'item') {
            const item = (quotation.items || []).find(i => i.id == itemId);
            if (!item) return '<p>Item not found.</p>';
            return `
                <div class="crm-form-grid">
                    <div class="crm-field" style="grid-column:1/-1;"><label class="crm-field-label">Product Name *</label>
                        <input class="crm-input" name="product_name" value="${esc(item.product_name)}" required>
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Capacity</label>
                        <input class="crm-input" name="capacity" value="${esc(item.capacity)}">
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Qty</label>
                        <input class="crm-input" type="number" step="0.01" min="0.01" name="quantity" value="${item.quantity}">
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Unit Price</label>
                        <input class="crm-input" type="number" step="0.01" min="0" name="unit_price" value="${item.unit_price}">
                    </div>
                    <div class="crm-field"><label class="crm-field-label">Tax %</label>
                        <input class="crm-input" type="number" step="0.01" min="0" name="tax_rate" value="${item.tax_rate}">
                    </div>
                </div>
                <div class="crm-field"><label class="crm-field-label">Description</label>
                    <textarea class="crm-input" name="description" rows="3">${esc(item.description)}</textarea>
                </div>
                <div class="crm-field"><label class="crm-field-label">Operation</label>
                    <textarea class="crm-input" name="operation" rows="3">${esc(item.operation)}</textarea>
                </div>
                <div class="crm-field"><label class="crm-field-label">Technical Specs (JSON)</label>
                    <textarea class="crm-input" name="technical_specifications" rows="4">${esc(jsonField(item.technical_specifications))}</textarea>
                </div>
                <div class="crm-field"><label class="crm-field-label">Input Specs (JSON)</label>
                    <textarea class="crm-input" name="input_specifications" rows="3">${esc(jsonField(item.input_specifications))}</textarea>
                </div>
                <div class="crm-field"><label class="crm-field-label">Salient Features (JSON array)</label>
                    <textarea class="crm-input" name="salient_features" rows="3">${esc(jsonField(item.salient_features))}</textarea>
                </div>
                <div class="crm-field"><label class="crm-field-label">Utility Requirements (JSON)</label>
                    <textarea class="crm-input" name="utility_requirements" rows="3">${esc(jsonField(item.utility_requirements))}</textarea>
                </div>
                <div class="crm-field">
                    <label class="crm-checkbox"><input type="checkbox" name="include_catalog" value="1" ${item.include_catalog ? 'checked' : ''}> Include catalog in PDF</label>
                </div>`;
        }
        return '';
    }

    function addEditItemRow(data = {}) {
        const tbody = document.getElementById('edit-items-body');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select class="crm-input item-product">
                    <option value="">Custom</option>
                    ${products.map(p => `<option value="${p.id}" data-name="${esc(p.name)}" data-price="${p.price}" data-tax="${p.tax_rate}" data-capacity="${esc(p.capacity || '')}" ${data.product_id == p.id ? 'selected' : ''}>${esc(p.name)}</option>`).join('')}
                </select>
                <input class="crm-input item-name" value="${esc(data.product_name || '')}" required style="margin-top:4px;">
            </td>
            <td><input class="crm-input item-capacity" value="${esc(data.capacity || '')}"></td>
            <td><input class="crm-input item-qty" type="number" step="0.01" min="0.01" value="${data.quantity || 1}" required></td>
            <td><input class="crm-input item-price" type="number" step="0.01" min="0" value="${data.unit_price || 0}" required></td>
            <td><input class="crm-input item-tax" type="number" step="0.01" min="0" value="${data.tax_rate ?? 18}"></td>
            <td><button type="button" class="crm-btn remove-item"><i class="bi bi-trash"></i></button></td>`;
        tbody.appendChild(tr);
    }

    function bindItemsUi() {
        const tbody = document.getElementById('edit-items-body');
        (quotation.items || []).forEach(item => addEditItemRow(item));
        if (!tbody.children.length) addEditItemRow();
        document.getElementById('edit-add-item')?.addEventListener('click', () => addEditItemRow());
        tbody.addEventListener('click', e => {
            if (e.target.closest('.remove-item') && tbody.children.length > 1) e.target.closest('tr').remove();
        });
        tbody.addEventListener('change', e => {
            const sel = e.target.closest('.item-product');
            if (!sel) return;
            const tr = sel.closest('tr');
            const opt = sel.selectedOptions[0];
            if (!opt?.value) return;
            tr.querySelector('.item-name').value = opt.dataset.name || '';
            tr.querySelector('.item-price').value = opt.dataset.price || 0;
            tr.querySelector('.item-tax').value = opt.dataset.tax || 18;
            tr.querySelector('.item-capacity').value = opt.dataset.capacity || '';
        });
    }

    document.querySelectorAll('.crm-section-edit').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.section, btn.dataset.itemId || null));
    });
    document.getElementById('section-modal-close').addEventListener('click', closeModal);
    document.getElementById('section-modal-cancel').addEventListener('click', closeModal);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        err.textContent = '';
        let payload = { section: currentSection };

        if (currentSection === 'items') {
            payload.tax_type = form.tax_type.value;
            payload.items = [...document.querySelectorAll('#edit-items-body tr')].map(tr => ({
                product_id: tr.querySelector('.item-product').value || null,
                product_name: tr.querySelector('.item-name').value,
                capacity: tr.querySelector('.item-capacity').value || null,
                quantity: parseFloat(tr.querySelector('.item-qty').value),
                unit_price: parseFloat(tr.querySelector('.item-price').value),
                tax_rate: parseFloat(tr.querySelector('.item-tax').value || 0),
                include_catalog: true,
            }));
        } else if (currentSection === 'item') {
            payload.item_id = Number(currentItemId);
            payload.product_name = form.product_name.value;
            payload.capacity = form.capacity.value || null;
            payload.description = form.description.value || null;
            payload.operation = form.operation.value || null;
            payload.technical_specifications = form.technical_specifications.value || null;
            payload.input_specifications = form.input_specifications.value || null;
            payload.salient_features = form.salient_features.value || null;
            payload.utility_requirements = form.utility_requirements.value || null;
            payload.quantity = parseFloat(form.quantity.value);
            payload.unit_price = parseFloat(form.unit_price.value);
            payload.tax_rate = parseFloat(form.tax_rate.value || 0);
            payload.include_catalog = form.include_catalog.checked;
        } else {
            const fd = new FormData(form);
            for (const [k, v] of fd.entries()) payload[k] = v === '' ? null : v;
        }

        const res = await fetch(`{{ route('quotations.section', $quotation) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (json.success) window.location.reload();
        else err.textContent = json.message || Object.values(json.errors || {}).flat().join(' ');
    });
    @endif
});
</script>
@endpush
