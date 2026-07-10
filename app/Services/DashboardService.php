<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, int|float>
     */
    public function getStats(?User $user = null): array
    {
        $query = Lead::query();

        if ($user && ! $user->can('leads.view') && $user->can('dashboard.view')) {
            $query->where('assigned_to', $user->id);
        } elseif ($user && ! $user->hasAnyRole(['Super Admin', 'Admin', 'Sales Manager'])) {
            $query->where('assigned_to', $user->id);
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $base = clone $query;

        $totalLeads = (clone $base)->count();
        $todayLeads = (clone $base)->whereDate('created_at', $today)->count();
        $yesterdayLeads = (clone $base)->whereDate('created_at', $yesterday)->count();
        $weeklyLeads = (clone $base)->where('created_at', '>=', $weekStart)->count();
        $monthlyLeads = (clone $base)->where('created_at', '>=', $monthStart)->count();

        $wonLeads = (clone $base)->where('status', LeadStatus::Won->value)->count();
        $lostLeads = (clone $base)->where('status', LeadStatus::Lost->value)->count();
        $pendingFollowups = (clone $base)->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now())
            ->whereNotIn('status', [LeadStatus::Won->value, LeadStatus::Lost->value, LeadStatus::Junk->value])
            ->count();

        $todayFollowups = (clone $base)->whereDate('next_followup_at', $today)->count();
        $revenue = (clone $base)->where('status', LeadStatus::Won->value)->sum('won_value');
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 2) : 0;

        return [
            'today_leads' => $todayLeads,
            'yesterday_leads' => $yesterdayLeads,
            'weekly_leads' => $weeklyLeads,
            'monthly_leads' => $monthlyLeads,
            'total_leads' => $totalLeads,
            'pending_followups' => $pendingFollowups,
            'today_followups' => $todayFollowups,
            'won_leads' => $wonLeads,
            'lost_leads' => $lostLeads,
            'conversion_rate' => $conversionRate,
            'revenue' => (float) $revenue,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getLeadSourceChart(): array
    {
        return Lead::query()
            ->select('lead_source_id', DB::raw('count(*) as total'))
            ->with('leadSource:id,name,color')
            ->groupBy('lead_source_id')
            ->get()
            ->map(fn ($row) => [
                'source' => $row->leadSource?->name ?? 'Unknown',
                'color' => $row->leadSource?->color ?? '#6c757d',
                'total' => (int) $row->total,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getLeadStatusChart(): array
    {
        return Lead::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'total' => (int) $row->total,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getTopSalesExecutives(int $limit = 5): array
    {
        return Lead::query()
            ->selectRaw('assigned_to, count(*) as total, sum(case when status = ? then 1 else 0 end) as won', [LeadStatus::Won->value])
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderByDesc('won')
            ->limit($limit)
            ->with('assignee:id,name')
            ->get()
            ->map(fn ($row) => [
                'user' => $row->assignee?->name ?? 'Unknown',
                'total_leads' => (int) $row->total,
                'won_leads' => (int) $row->won,
            ])
            ->values()
            ->all();
    }
}
