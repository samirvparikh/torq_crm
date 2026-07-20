<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()->can('settings.view') || auth()->user()->can('settings.edit'), 403);

        $logoPath = Setting::getValue('company', 'logo');

        return view('settings.company', [
            'company' => Setting::getGroup('company'),
            'bank' => Setting::getGroup('bank'),
            'logoUrl' => $this->logoDataUri(is_string($logoPath) ? $logoPath : null),
            'canEdit' => auth()->user()->can('settings.edit'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:2000'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'branch' => ['nullable', 'string', 'max:191'],
            'signatory_name' => ['nullable', 'string', 'max:191'],
            'signatory_phone' => ['nullable', 'string', 'max:50'],
            'intro_text' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],
            'account_name' => ['nullable', 'string', 'max:191'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:191'],
            'bank_branch' => ['nullable', 'string', 'max:191'],
            'ifsc' => ['nullable', 'string', 'max:50'],
        ]);

        $companyKeys = [
            'name', 'email', 'phone', 'website', 'address', 'gst_number',
            'branch', 'signatory_name', 'signatory_phone', 'intro_text',
        ];

        foreach ($companyKeys as $key) {
            if (array_key_exists($key, $data)) {
                $type = in_array($key, ['address', 'intro_text'], true) ? 'text' : 'string';
                Setting::setValue('company', $key, $data[$key] ?? '', $type);
            }
        }

        Setting::setValue('bank', 'account_name', $data['account_name'] ?? '');
        Setting::setValue('bank', 'account_number', $data['account_number'] ?? '');
        Setting::setValue('bank', 'bank_name', $data['bank_name'] ?? '');
        Setting::setValue('bank', 'branch', $data['bank_branch'] ?? '');
        Setting::setValue('bank', 'ifsc', $data['ifsc'] ?? '');

        $currentLogo = Setting::getValue('company', 'logo');

        if ($request->boolean('remove_logo') && $currentLogo) {
            Storage::disk('public')->delete($currentLogo);
            Setting::setValue('company', 'logo', '');
            $currentLogo = null;
        }

        if ($request->hasFile('logo')) {
            if ($currentLogo) {
                Storage::disk('public')->delete($currentLogo);
            }
            $path = $request->file('logo')->store('company', 'public');
            Setting::setValue('company', 'logo', $path);
        }

        return back()->with('status', 'Company profile updated successfully.');
    }

    public function logo(): Response
    {
        abort_unless(auth()->user()->can('settings.view') || auth()->user()->can('settings.edit'), 403);

        $path = Setting::getValue('company', 'logo');

        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($path));
    }

    protected function logoDataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolute = Storage::disk('public')->path($path);
        $mime = @mime_content_type($absolute) ?: 'image/png';
        $binary = @file_get_contents($absolute);

        if ($binary === false) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }
}
