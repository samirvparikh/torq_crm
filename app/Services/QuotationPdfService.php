<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationTermTemplate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class QuotationPdfService
{
    public function companyProfile(): array
    {
        $logo = Setting::getValue('company', 'logo', '');

        return [
            'name' => Setting::getValue('company', 'name', config('app.name')),
            'email' => Setting::getValue('company', 'email', ''),
            'phone' => Setting::getValue('company', 'phone', ''),
            'website' => Setting::getValue('company', 'website', ''),
            'address' => Setting::getValue('company', 'address', ''),
            'gst_number' => Setting::getValue('company', 'gst_number', ''),
            'signatory_name' => Setting::getValue('company', 'signatory_name', ''),
            'signatory_phone' => Setting::getValue('company', 'signatory_phone', ''),
            'intro_text' => Setting::getValue('company', 'intro_text', ''),
            'logo' => $logo,
            'logo_data_uri' => $this->logoDataUri(is_string($logo) ? $logo : null),
        ];
    }

    public function bankProfile(): array
    {
        return Setting::getGroup('bank');
    }

    /**
     * @return array<string, mixed>
     */
    public function documentViewData(Quotation $quotation): array
    {
        $quotation->loadMissing(['items.product', 'customer.addresses', 'company', 'lead', 'termTemplate', 'creator']);

        return [
            'quotation' => $quotation,
            'company' => $this->companyProfile(),
            'bank' => $this->bankProfile(),
            'termsContent' => $quotation->terms
                ?: $quotation->termTemplate?->content
                ?: QuotationTermTemplate::query()->where('is_default', true)->value('content'),
        ];
    }

    public function render(Quotation $quotation)
    {
        $pdf = Pdf::loadView('quotations.pdf', $this->documentViewData($quotation))->setPaper('a4');
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans');
        $size = 9;
        $sample = 'Page 0 of 0';
        $width = $fontMetrics->getTextWidth($sample, $font, $size);
        $x = ($canvas->get_width() - $width) / 2;
        $y = $canvas->get_height() - 28;

        $canvas->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size, [0.4, 0.4, 0.4]);

        return $dompdf;
    }

    public function download(Quotation $quotation)
    {
        $filename = ($quotation->quotation_number ?: 'quotation').'.pdf';

        return response($this->render($quotation)->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function stream(Quotation $quotation)
    {
        $filename = ($quotation->quotation_number ?: 'quotation').'.pdf';

        return response($this->render($quotation)->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function store(Quotation $quotation): string
    {
        $relative = 'quotations/'.($quotation->quotation_number ?: $quotation->id).'.pdf';
        Storage::disk('public')->put($relative, $this->render($quotation)->output());

        $quotation->update(['pdf_path' => $relative]);

        return $relative;
    }

    /**
     * Snapshot catalog fields from a product onto a quotation line item payload.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function snapshotProductCatalog(array $item, ?Product $product = null): array
    {
        if (! $product) {
            return $item;
        }

        $item['product_name'] = $item['product_name'] ?? $product->name;
        $item['description'] = $item['description'] ?? $product->description;
        $item['capacity'] = $item['capacity'] ?? $product->capacity;
        $item['operation'] = $item['operation'] ?? $product->operation;
        $item['technical_specifications'] = $item['technical_specifications'] ?? $product->technical_specifications;
        $item['input_specifications'] = $item['input_specifications'] ?? $product->input_specifications;
        $item['salient_features'] = $item['salient_features'] ?? $product->salient_features;
        $item['utility_requirements'] = $item['utility_requirements'] ?? $product->utility_requirements;
        $item['unit'] = $item['unit'] ?? $product->unit;
        $item['unit_price'] = $item['unit_price'] ?? $product->price;
        $item['tax_rate'] = $item['tax_rate'] ?? $product->tax_rate;

        return $item;
    }

    protected function logoDataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolute = Storage::disk('public')->path($path);
        $mime = mime_content_type($absolute) ?: 'image/png';
        $binary = @file_get_contents($absolute);

        if ($binary === false) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }
}
