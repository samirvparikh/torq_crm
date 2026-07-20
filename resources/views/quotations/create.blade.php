@extends('layouts.crm')

@section('title', 'Create Quotation')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a><span>&rsaquo;</span>
    <a href="{{ route('quotations.index') }}">Quotations</a><span>&rsaquo;</span> Create
@endsection

@section('toolbar')
    <div class="crm-page-title"><i class="bi bi-plus-circle"></i> Create Quotation</div>
    <div class="crm-toolbar-actions">
        <a href="{{ route('quotations.index') }}" class="crm-btn">Back</a>
    </div>
@endsection

@section('content')
    <div class="crm-content-card">
        <div class="crm-content-card-body">
            <form id="quotation-form">
                @csrf
                <div class="crm-form-grid">
                    <div class="crm-field">
                        <label class="crm-field-label">Customer</label>
                        <select class="crm-input" name="customer_id">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Quotation Date *</label>
                        <input class="crm-input" type="date" name="quotation_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Valid Until</label>
                        <input class="crm-input" type="date" name="valid_until">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Tax Type</label>
                        <select class="crm-input" name="tax_type">
                            <option value="igst">IGST 18%</option>
                            <option value="cgst_sgst">CGST 9% + SGST 9%</option>
                        </select>
                    </div>
                    <div class="crm-field" style="grid-column:1/-1;">
                        <label class="crm-field-label">Subject</label>
                        <input class="crm-input" name="subject" placeholder="Automatic Liquid Filling Machine">
                    </div>
                    <div class="crm-field">
                        <label class="crm-field-label">Terms Template</label>
                        <select class="crm-input" name="quotation_term_template_id" id="term-template">
                            <option value="">Custom / Default</option>
                            @foreach ($termTemplates as $t)
                                <option value="{{ $t->id }}" data-content="{{ e($t->content) }}" @selected($t->is_default)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Intro Text</label>
                    <textarea class="crm-input" name="intro_text" rows="3" placeholder="Leave blank to use company default intro"></textarea>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Line Items</label>
                    <div class="crm-table-wrap">
                        <table class="crm-line-items-table" id="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Capacity</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Tax %</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="items-body"></tbody>
                        </table>
                    </div>
                    <button type="button" class="crm-btn" id="add-item" style="margin-top:8px;"><i class="bi bi-plus"></i> Add Item</button>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Terms &amp; Conditions</label>
                    <textarea class="crm-input" name="terms" id="terms-content" rows="5"></textarea>
                </div>

                <div class="crm-field">
                    <label class="crm-field-label">Notes</label>
                    <textarea class="crm-input" name="notes" rows="2"></textarea>
                </div>

                <div class="crm-toolbar-actions" style="margin-top:12px;">
                    <button type="submit" class="crm-btn crm-btn-primary-sm">Save Quotation</button>
                    <a href="{{ route('quotations.index') }}" class="crm-btn">Cancel</a>
                </div>
                <p id="form-error" class="crm-auth-error"></p>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const products = @json($products);
    const itemsBody = document.getElementById('items-body');
    const form = document.getElementById('quotation-form');

    function addItemRow(data = {}) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select class="crm-input item-product">
                    <option value="">Custom / type below</option>
                    ${products.map(p => `<option value="${p.id}" data-name="${p.name}" data-price="${p.price}" data-tax="${p.tax_rate}" data-capacity="${p.capacity || ''}" ${data.product_id == p.id ? 'selected' : ''}>${p.name}</option>`).join('')}
                </select>
                <input class="crm-input item-name" value="${data.product_name || ''}" placeholder="Product name" required style="margin-top:4px;">
            </td>
            <td><input class="crm-input item-capacity" value="${data.capacity || ''}" placeholder="100 ml to 5 Ltr"></td>
            <td><input class="crm-input item-qty" type="number" step="0.01" min="0.01" value="${data.quantity || 1}" required></td>
            <td><input class="crm-input item-price" type="number" step="0.01" min="0" value="${data.unit_price || 0}" required></td>
            <td><input class="crm-input item-tax" type="number" step="0.01" min="0" value="${data.tax_rate ?? 18}"></td>
            <td><button type="button" class="crm-btn remove-item"><i class="bi bi-trash"></i></button></td>`;
        itemsBody.appendChild(tr);
    }

    addItemRow();

    const defaultOpt = document.getElementById('term-template').selectedOptions[0];
    if (defaultOpt?.dataset.content) {
        document.getElementById('terms-content').value = defaultOpt.dataset.content;
    }

    document.getElementById('add-item').addEventListener('click', () => addItemRow());

    itemsBody.addEventListener('click', e => {
        if (e.target.closest('.remove-item') && itemsBody.children.length > 1) {
            e.target.closest('tr').remove();
        }
    });

    itemsBody.addEventListener('change', e => {
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

    document.getElementById('term-template').addEventListener('change', e => {
        const opt = e.target.selectedOptions[0];
        if (opt?.dataset.content) {
            document.getElementById('terms-content').value = opt.dataset.content;
        }
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const items = [...itemsBody.querySelectorAll('tr')].map(tr => ({
            product_id: tr.querySelector('.item-product').value || null,
            product_name: tr.querySelector('.item-name').value,
            capacity: tr.querySelector('.item-capacity').value || null,
            quantity: parseFloat(tr.querySelector('.item-qty').value),
            unit_price: parseFloat(tr.querySelector('.item-price').value),
            tax_rate: parseFloat(tr.querySelector('.item-tax').value || 0),
            include_catalog: true,
        }));

        const payload = {
            customer_id: form.customer_id.value || null,
            subject: form.subject.value || null,
            intro_text: form.intro_text.value || null,
            quotation_date: form.quotation_date.value,
            valid_until: form.valid_until.value || null,
            tax_type: form.tax_type.value,
            quotation_term_template_id: form.quotation_term_template_id.value || null,
            terms: form.terms.value || null,
            notes: form.notes.value || null,
            items,
        };

        const res = await fetch('{{ route('quotations.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });

        const json = await res.json();
        if (json.success && json.data?.id) {
            window.location.href = `{{ url('quotations') }}/${json.data.id}`;
        } else {
            document.getElementById('form-error').textContent =
                json.message || Object.values(json.errors || {}).flat().join(' ');
        }
    });
});
</script>
@endpush
