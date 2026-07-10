<?php

namespace App\Http\Requests\Lead;

use App\Enums\LeadPriority;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lead = $this->route('lead');

        return $lead && $this->user()?->can('update', $lead);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $leadId = $this->route('lead')?->id;

        return [
            'lead_source_id' => ['sometimes', 'nullable', 'exists:lead_sources,id'],
            'indiamart_lead_id' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('leads', 'indiamart_lead_id')->ignore($leadId)],
            'customer_id' => ['sometimes', 'nullable', 'exists:customers,id'],
            'company_id' => ['sometimes', 'nullable', 'exists:companies,id'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'customer_name' => ['sometimes', 'required', 'string', 'max:191'],
            'company_name' => ['sometimes', 'nullable', 'string', 'max:191'],
            'gst_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'mobile' => ['sometimes', 'nullable', 'string', 'max:20'],
            'alternate_mobile' => ['sometimes', 'nullable', 'string', 'max:20'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email', 'max:191'],
            'website' => ['sometimes', 'nullable', 'string', 'max:191'],
            'address' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'pincode' => ['sometimes', 'nullable', 'string', 'max:10'],
            'interested_product' => ['sometimes', 'nullable', 'string', 'max:191'],
            'requirement' => ['sometimes', 'nullable', 'string'],
            'quantity' => ['sometimes', 'nullable', 'string', 'max:100'],
            'budget' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'priority' => ['sometimes', Rule::in(LeadPriority::values())],
            'status' => ['sometimes', Rule::in(LeadStatus::values())],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'expected_closing_date' => ['sometimes', 'nullable', 'date'],
            'remarks' => ['sometimes', 'nullable', 'string'],
            'lost_reason' => ['sometimes', 'nullable', 'string', 'max:191'],
            'won_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'next_followup_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
