<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\LeadStatus;
use App\Events\LeadCreated;
use App\Models\IndiaMartLead;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndiaMartLeadSyncService
{
    public function __construct(
        protected LeadService $leadService,
    ) {}

    /**
     * Copy new rows from inidamart_leads into leads.
     *
     * @return array{inserted:int, skipped:int, total:int}
     */
    public function sync(?User $actor = null): array
    {
        $sourceId = $this->resolveIndiaMartSourceId();
        $inserted = 0;
        $skipped = 0;

        IndiaMartLead::query()
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($sourceId, $actor, &$inserted, &$skipped) {
                foreach ($rows as $row) {
                    $uniqueId = trim((string) $row->unique_query_id);
                    if ($uniqueId === '') {
                        $skipped++;
                        continue;
                    }

                    if ($this->leadService->findByIndiamartId($uniqueId)) {
                        $skipped++;
                        continue;
                    }

                    try {
                        DB::transaction(function () use ($row, $uniqueId, $sourceId, $actor, &$inserted) {
                            $payload = $this->mapToLeadPayload($row, $uniqueId, $sourceId, $actor);
                            $lead = Lead::query()->create($payload);

                            if ($actor) {
                                $this->leadService->logActivity(
                                    $lead,
                                    ActivityType::LeadCreated,
                                    'Lead synced from IndiaMART Message Centre',
                                    $actor,
                                    ['indiamart_lead_id' => $uniqueId]
                                );
                                LeadCreated::dispatch($lead, $actor);
                            }

                            $inserted++;
                        });
                    } catch (QueryException $e) {
                        // Unique indiamart_lead_id / race — treat as already inserted
                        if (($e->errorInfo[1] ?? null) === 1062) {
                            $skipped++;
                            continue;
                        }
                        throw $e;
                    }
                }
            });

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'total' => $inserted + $skipped,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapToLeadPayload(IndiaMartLead $row, string $uniqueId, ?int $sourceId, ?User $actor): array
    {
        $customerName = trim((string) ($row->sender_name ?: $row->sender_company ?: 'IndiaMART Lead'));
        $country = $this->mapCountry($row->sender_country_iso);

        return [
            'lead_number' => $this->leadService->generateLeadNumber(),
            'lead_source_id' => $sourceId,
            'indiamart_lead_id' => Str::limit($uniqueId, 100, ''),
            'query_type' => $row->query_type,
            'query_time' => $row->query_time,
            'query_mcat_name' => $row->query_mcat_name,
            'call_duration' => $row->call_duration,
            'receiver_mobile' => $row->receiver_mobile,
            'customer_name' => Str::limit($customerName, 191, ''),
            'company_name' => $this->clip($row->sender_company, 191),
            'mobile' => $this->clip($row->sender_mobile, 20),
            'alternate_mobile' => $this->clip($row->sender_mobile_alt, 20),
            'whatsapp' => $this->clip($row->sender_mobile, 20),
            'email' => $this->clip($row->sender_email ?: $row->sender_email_alt, 191),
            'address' => $row->sender_address,
            'city' => $this->clip($row->sender_city, 100),
            'state' => $this->clip($row->sender_state, 100),
            'country' => $country,
            'pincode' => $this->clip($row->sender_pincode, 10),
            'interested_product' => $this->clip($row->query_product_name, 191),
            'requirement' => $row->query_message,
            'remarks' => $this->clip($row->query_mcat_name, 191),
            'priority' => 'Medium',
            'status' => LeadStatus::New->value,
            'created_by' => $actor?->id,
            'raw_data' => $row->toArray(),
        ];
    }

    protected function resolveIndiaMartSourceId(): ?int
    {
        $source = LeadSource::query()
            ->where(function ($query) {
                $query->where('slug', 'indiamart')
                    ->orWhere('name', 'IndiaMART');
            })
            ->first();

        if ($source) {
            return $source->id;
        }

        return LeadSource::query()->create([
            'name' => 'IndiaMART',
            'slug' => 'indiamart',
            'color' => '#e74c3c',
            'icon' => 'shop',
            'is_active' => true,
            'sort_order' => 1,
        ])->id;
    }

    protected function mapCountry(?string $iso): string
    {
        $iso = strtoupper(trim((string) $iso));

        return match ($iso) {
            'IN', 'IND' => 'India',
            '' => 'India',
            default => $iso,
        };
    }

    protected function clip(mixed $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '' || strtolower($text) === 'null') {
            return null;
        }

        return Str::limit($text, $max, '');
    }
}
