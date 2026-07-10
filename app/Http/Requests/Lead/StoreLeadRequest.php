<?php

namespace App\Http\Requests\Lead;

use App\Enums\LeadPriority;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lead_source_id' => ['nullable', 'exists:lead_sources,id'],
            'indiamart_lead_id' => ['nullable', 'string', 'max:100', 'unique:leads,indiamart_lead_id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'customer_name' => ['required', 'string', 'max:191'],
            'company_name' => ['nullable', 'string', 'max:191'],
            'gst_number' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:191'],
            'website' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'interested_product' => ['nullable', 'string', 'max:191'],
            'requirement' => ['nullable', 'string'],
            'quantity' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', Rule::in(LeadPriority::values())],
            'status' => ['nullable', Rule::in(LeadStatus::values())],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'expected_closing_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
            'next_followup_at' => ['nullable', 'date'],
        ];
    }
}
