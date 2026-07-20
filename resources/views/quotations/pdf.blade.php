<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quotation_number }}</title>
    @include('quotations.partials.document-styles', ['context' => 'pdf'])
</head>
<body class="quotation-document">
    <div class="page-header">
        @include('quotations.partials.document-header', ['company' => $company])
    </div>

    <div class="page-footer">
        <span class="page-footer-line"></span>
    </div>

    @include('quotations.partials.document-body', [
        'quotation' => $quotation,
        'company' => $company,
        'bank' => $bank,
        'termsContent' => $termsContent,
    ])
</body>
</html>
