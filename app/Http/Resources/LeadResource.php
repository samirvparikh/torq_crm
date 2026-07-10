<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Lead */
class LeadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_number' => $this->lead_number,
            'indiamart_lead_id' => $this->indiamart_lead_id,
            'customer_name' => $this->customer_name,
            'company_name' => $this->company_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'city' => $this->city,
            'state' => $this->state,
            'interested_product' => $this->interested_product,
            'budget' => $this->budget,
            'priority' => $this->priority?->value,
            'status' => $this->status?->value,
            'expected_closing_date' => $this->expected_closing_date?->format('Y-m-d'),
            'next_followup_at' => $this->next_followup_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'lead_source' => $this->whenLoaded('leadSource', fn () => [
                'id' => $this->leadSource?->id,
                'name' => $this->leadSource?->name,
                'color' => $this->leadSource?->color,
            ]),
            'assignee' => $this->whenLoaded('assignee', fn () => [
                'id' => $this->assignee?->id,
                'name' => $this->assignee?->name,
            ]),
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ]),
        ];
    }
}
