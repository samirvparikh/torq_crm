<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->can('permissions.view'), 403);

        return view('permissions.index', [
            'groups' => $this->permissionService->groups(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('permissions.view'), 403);

        $permissions = $this->permissionService->list(
            $request->only(['search', 'group', 'sort_by', 'sort_dir']),
            (int) $request->input('per_page', 50)
        );

        $data = collect($permissions->items())->map(function (Permission $permission) {
            $group = strstr($permission->name, '.', true) ?: 'other';

            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => ucfirst($group),
                'guard_name' => $permission->guard_name,
                'roles_count' => $permission->roles_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ],
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('permissions.edit'), 403);

        $created = $this->permissionService->syncFromRegistry();

        return response()->json([
            'success' => true,
            'message' => $created
                ? "Sync complete: {$created} new permission(s) added."
                : 'Sync complete: permissions are already up to date.',
            'data' => ['created' => $created],
        ]);
    }
}
