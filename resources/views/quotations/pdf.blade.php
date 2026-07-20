<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quotation_number }}</title>
    @include('quotations.partials.document-styles', ['context' => 'pdf'])
</head>
<body class="quotation-document">
@include('quotations.partials.document-body', [
    'quotation' => $quotation,
    'company' => $company,
    'bank' => $bank,
    'termsContent' => $termsContent,
])
</body>
</html>
