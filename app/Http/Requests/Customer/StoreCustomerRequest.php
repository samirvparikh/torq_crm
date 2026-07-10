<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('customers.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['nullable', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:20'],
            'pan' => ['nullable', 'string', 'max:15'],
            'website' => ['nullable', 'url', 'max:191'],
            'designation' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'contact_persons' => ['nullable', 'array'],
            'contact_persons.*.name' => ['required_with:contact_persons', 'string', 'max:191'],
            'contact_persons.*.email' => ['nullable', 'email', 'max:191'],
            'contact_persons.*.mobile' => ['nullable', 'string', 'max:20'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.address' => ['required_with:addresses', 'string'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
        ];
    }
}
