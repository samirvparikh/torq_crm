@php $context = $context ?? 'pdf'; @endphp
@if ($context === 'pdf')
<style>
    @page { margin: 28px 32px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; line-height: 1.45; }
@else
<style>
    .quotation-document {
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 11px;
        color: #111;
        line-height: 1.45;
    }
@endif
    .quotation-document .header,
    .header { border-bottom: 2px solid #1b3c6a; padding-bottom: 10px; margin-bottom: 14px; }
    .quotation-document .header-table,
    .header-table { width: 100%; border-collapse: collapse; }
    .quotation-document .header-table td,
    .header-table td { border: none; vertical-align: top; padding: 0; }
    .quotation-document .header-logo-cell,
    .header-logo-cell { width: 150px; padding-right: 10px; vertical-align: middle; }
    .quotation-document .header-logo,
    .header-logo { max-height: 95px; max-width: 145px; }
    .quotation-document .header-info,
    .header-info { text-align: center; vertical-align: middle; }
    .quotation-document .header-info h1,
    .header-info h1 { margin: 0; font-size: 18px; color: #1b3c6a; letter-spacing: 0.5px; text-align: center; }
    .quotation-document .header-contact-cell,
    .header-contact-cell { width: 170px; vertical-align: middle; text-align: right; font-size: 10px; color: #333; line-height: 1.55; white-space: nowrap; }
    .quotation-document .header-address,
    .header-address { margin: 10px 0 0; font-size: 10px; color: #333; text-align: center; }
    .quotation-document .meta,
    .meta { width: 100%; margin-bottom: 12px; }
    .quotation-document .meta td,
    .meta td { vertical-align: top; }
    .quotation-document .meta .right,
    .meta .right { text-align: right; }
    .quotation-document .subject,
    .subject { font-weight: bold; margin: 10px 0 8px; }
    .quotation-document .section,
    .section { margin: 16px 0 10px; page-break-inside: avoid; break-inside: avoid; }
    .quotation-document .section-title,
    .section-title { font-size: 13px; font-weight: bold; color: #1b3c6a; border-bottom: 1px solid #c5ced9; padding-bottom: 3px; margin-bottom: 6px; }
    .quotation-document .subhead,
    .subhead { font-weight: bold; margin: 8px 0 3px; }
    .quotation-document table.spec,
    table.spec { width: 100%; border-collapse: collapse; margin: 4px 0 8px; }
    .quotation-document table.spec td,
    table.spec td { border: 1px solid #d0d5dd; padding: 4px 6px; vertical-align: top; }
    .quotation-document table.spec td.k,
    table.spec td.k { width: 38%; background: #f3f5f8; font-weight: bold; }
    .quotation-document ul.features,
    ul.features { margin: 4px 0 8px 16px; padding: 0; }
    .quotation-document ul.features li,
    ul.features li { margin-bottom: 2px; }
    .quotation-document table.price,
    table.price { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .quotation-document table.price th,
    .quotation-document table.price td,
    table.price th, table.price td { border: 1px solid #b8c0cc; padding: 5px 6px; }
    .quotation-document table.price th,
    table.price th { background: #1b3c6a; color: #fff; font-size: 10px; text-transform: uppercase; }
    .quotation-document table.price td.num,
    .quotation-document table.price th.num,
    table.price td.num, table.price th.num { text-align: right; }
    .quotation-document table.price td.center,
    table.price td.center { text-align: center; }
    .quotation-document .totals,
    .totals { width: 45%; margin-left: auto; border-collapse: collapse; margin-top: 8px; }
    .quotation-document .totals td,
    .totals td { padding: 4px 6px; border: 1px solid #d0d5dd; }
    .quotation-document .totals td.l,
    .totals td.l { font-weight: bold; background: #f3f5f8; }
    .quotation-document .totals td.r,
    .totals td.r { text-align: right; }
    .quotation-document .totals tr.grand td,
    .totals tr.grand td { background: #e8eef6; font-weight: bold; }
    .quotation-document .bank,
    .bank { margin-top: 12px; padding: 8px; border: 1px solid #d0d5dd; background: #fafbfc; }
    .quotation-document .bank strong,
    .bank strong { display: block; margin-bottom: 4px; color: #1b3c6a; }
    .quotation-document .sign,
    .sign { margin-top: 28px; }
    .quotation-document .muted,
    .muted { color: #555; font-size: 10px; }
    .quotation-document .terms,
    .terms { white-space: pre-wrap; font-size: 10px; }
@if ($context === 'screen')
    .quotation-preview-shell {
        background: #525659;
        margin: 0 -24px;
        padding: 24px 16px 32px;
    }
    .quotation-preview-meta {
        max-width: 210mm;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        color: #e8eaed;
        font-size: 12px;
    }
    .quotation-preview-meta .crm-badge {
        font-size: 11px;
    }
    .quotation-pages {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    .quotation-doc-page {
        width: 210mm;
        min-height: 297mm;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.28);
        box-sizing: border-box;
        padding: 28px 32px;
        position: relative;
    }
    .quotation-doc-page::after {
        content: attr(data-page);
        position: absolute;
        bottom: 10px;
        right: 16px;
        font-size: 10px;
        color: #888;
    }
    .quotation-doc-source {
        position: absolute;
        left: -9999px;
        top: 0;
        width: 210mm;
        visibility: hidden;
        pointer-events: none;
    }
@endif
</style>
