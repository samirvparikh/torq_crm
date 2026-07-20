@php
    $fmt = $fmt ?? fn ($n) => number_format((float) $n, 2);
    $taxType = $taxType ?? ($quotation->tax_type ?: 'igst');
@endphp

<div class="quotation-block header">
    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                @if (!empty($company['logo_data_uri']))
                    <img class="header-logo" src="{{ $company['logo_data_uri'] }}" alt="Logo">
                @endif
            </td>
            <td class="header-info">
                <h1>{{ strtoupper($company['name'] ?? 'TORQ PACKAGING SOLUTION') }}</h1>
            </td>
            <td class="header-contact-cell">
                @if (!empty($company['email']))
                    <div>{{ $company['email'] }}</div>
                @endif
                @if (!empty($company['website']))
                    <div>{{ $company['website'] }}</div>
                @endif
                @if (!empty($company['phone']))
                    <div>{{ $company['phone'] }}</div>
                @endif
            </td>
        </tr>
    </table>
    @if (!empty($company['address']))
        <p class="header-address">{{ $company['address'] }}</p>
    @endif
</div>

<div class="quotation-block">
    <table class="meta">
        <tr>
            <td>
                <strong>To,</strong><br>
                {{ $quotation->customer?->name ?? $quotation->company?->name ?? $quotation->lead?->customer_name ?? 'Valued Customer' }}<br>
                @php
                    $address = $quotation->customer?->addresses?->firstWhere('is_primary', true)
                        ?? $quotation->customer?->addresses?->first();
                    $city = $address?->city ?? $quotation->lead?->city;
                    $state = $address?->state ?? $quotation->lead?->state;
                @endphp
                @if ($city || $state)
                    {{ collect([$city, $state])->filter()->implode(', ') }}
                @endif
            </td>
            <td class="right">
                <strong>Quotation No:</strong> {{ $quotation->quotation_number }}<br>
                <strong>Date:</strong> {{ optional($quotation->quotation_date)->format('d/m/Y') }}<br>
                @if ($quotation->valid_until)
                    <strong>Valid Until:</strong> {{ $quotation->valid_until->format('d/m/Y') }}
                @endif
            </td>
        </tr>
    </table>
</div>

@if ($quotation->subject)
    <div class="quotation-block subject">Sub: {{ $quotation->subject }}</div>
@endif

<div class="quotation-block">
    <p>Dear Sir / Madam,</p>
    <p>{{ $quotation->intro_text ?: ($company['intro_text'] ?? '') }}</p>
</div>

<div class="quotation-block sign">
    Thanks &amp; Regards<br>
    <strong>{{ $quotation->signatory_name ?: ($company['signatory_name'] ?? '') }}</strong><br>
    {{ $quotation->signatory_phone ?: ($company['signatory_phone'] ?? '') }}
</div>

@foreach ($quotation->items as $item)
    @if ($item->include_catalog && ($item->description || $item->operation || $item->technical_specifications || $item->salient_features))
        <div class="quotation-block section">
            <div class="section-title">{{ $item->product_name }}</div>

            @if ($item->description)
                <div class="subhead">Description</div>
                <p>{{ $item->description }}</p>
            @endif

            @if ($item->operation)
                <div class="subhead">Operation</div>
                <p>{{ $item->operation }}</p>
            @endif

            @if (!empty($item->technical_specifications))
                <div class="subhead">Technical Specifications</div>
                <table class="spec">
                    @foreach ($item->technical_specifications as $key => $value)
                        <tr>
                            <td class="k">{{ is_int($key) ? 'Specification' : $key }}</td>
                            <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if (!empty($item->input_specifications))
                <div class="subhead">Input Specification</div>
                <table class="spec">
                    @foreach ($item->input_specifications as $key => $value)
                        <tr>
                            <td class="k">{{ is_int($key) ? 'Input' : $key }}</td>
                            <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if (!empty($item->salient_features))
                <div class="subhead">Salient Features</div>
                <ul class="features">
                    @foreach ($item->salient_features as $feature)
                        <li>{{ is_array($feature) ? implode(' — ', $feature) : $feature }}</li>
                    @endforeach
                </ul>
            @endif

            @if (!empty($item->utility_requirements))
                <div class="subhead">Utility Requirement</div>
                <table class="spec">
                    @foreach ($item->utility_requirements as $key => $value)
                        <tr>
                            <td class="k">{{ is_int($key) ? 'Utility' : $key }}</td>
                            <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @endif
@endforeach

<div class="quotation-block section">
    <div class="section-title">Price Breakup</div>
    <table class="price">
        <thead>
            <tr>
                <th style="width:8%;">S.N.</th>
                <th>Particulars</th>
                <th style="width:18%;">Capacity</th>
                <th class="num" style="width:10%;">Qty</th>
                <th class="num" style="width:18%;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->capacity }}</td>
                    <td class="num">{{ $fmt($item->quantity) }}</td>
                    <td class="num">{{ $fmt(($item->quantity * $item->unit_price) - $item->discount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="l">Subtotal</td>
            <td class="r">{{ $fmt($quotation->subtotal) }}</td>
        </tr>
        @if ((float) $quotation->discount_amount > 0)
            <tr>
                <td class="l">Discount</td>
                <td class="r">- {{ $fmt($quotation->discount_amount) }}</td>
            </tr>
        @endif
        @if ($taxType === 'cgst_sgst')
            <tr>
                <td class="l">CGST @ 9%</td>
                <td class="r">{{ $fmt(((float) $quotation->tax_amount) / 2) }}</td>
            </tr>
            <tr>
                <td class="l">SGST @ 9%</td>
                <td class="r">{{ $fmt(((float) $quotation->tax_amount) / 2) }}</td>
            </tr>
        @else
            <tr>
                <td class="l">IGST @ 18%</td>
                <td class="r">{{ $fmt($quotation->tax_amount) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td class="l">Total Amount</td>
            <td class="r">{{ $fmt($quotation->total) }}</td>
        </tr>
    </table>
</div>

@if (!empty($bank))
    <div class="quotation-block bank">
        <strong>BANK DETAIL</strong>
        Account name :- {{ $bank['account_name'] ?? '' }}<br>
        Account number :- {{ $bank['account_number'] ?? '' }}<br>
        Bank name :- {{ $bank['bank_name'] ?? '' }}<br>
        Branch :- {{ $bank['branch'] ?? '' }}<br>
        IFSC :- {{ $bank['ifsc'] ?? '' }}<br>
        @if (!empty($company['gst_number']))
            COMPANY GSTIN : {{ $company['gst_number'] }}
        @endif
    </div>
@endif

@if ($termsContent)
    <div class="quotation-block section">
        <div class="section-title">Terms &amp; Conditions</div>
        <div class="terms">{{ $termsContent }}</div>
        <p class="muted" style="margin-top:16px;">For, {{ $company['name'] ?? 'TORQ Packaging Solution' }}</p>
    </div>
@endif

@if ($quotation->notes)
    <div class="quotation-block section">
        <div class="section-title">Notes</div>
        <p>{{ $quotation->notes }}</p>
    </div>
@endif
