<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', \App\Models\Lead::class);

        return view('dashboard.index', [
            'stats' => $this->dashboardService->getStats($request->user()),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Lead::class);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $this->dashboardService->getStats($request->user()),
                'lead_source_chart' => $this->dashboardService->getLeadSourceChart(),
                'lead_status_chart' => $this->dashboardService->getLeadStatusChart(),
                'top_sales_executives' => $this->dashboardService->getTopSalesExecutives(),
            ],
        ]);
    }
}
