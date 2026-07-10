<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $customer = $this->route('customer');

        return $customer && $this->user()?->can('update', $customer);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'nullable', 'exists:companies,id'],
            'name' => ['sometimes', 'required', 'string', 'max:191'],
            'email' => ['sometimes', 'nullable', 'email', 'max:191'],
            'mobile' => ['sometimes', 'nullable', 'string', 'max:20'],
            'alternate_mobile' => ['sometimes', 'nullable', 'string', 'max:20'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'gst_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'pan' => ['sometimes', 'nullable', 'string', 'max:15'],
            'website' => ['sometimes', 'nullable', 'url', 'max:191'],
            'designation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
