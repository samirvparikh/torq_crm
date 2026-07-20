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
