<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('users.index', [
            'roles' => Role::query()->orderBy('name')->pluck('name'),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->list(
            $request->only(['search', 'is_active', 'role', 'sort_by', 'sort_dir']),
            (int) $request->input('per_page', 25)
        );

        $data = collect($users->items())->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'designation' => $user->designation,
                'is_active' => $user->is_active,
                'role' => $user->primaryRoleName(),
                'roles' => $user->roles->pluck('name'),
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'string', Rule::in(RoleName::values())],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($data['role'] === RoleName::SuperAdmin->value && ! $request->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only Super Admin can create Super Admin users.'], 403);
        }

        $user = $this->userService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:191'],
            'email' => ['sometimes', 'required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'designation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'role' => ['sometimes', 'required', 'string', Rule::in(RoleName::values())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (
            isset($data['role'])
            && $data['role'] === RoleName::SuperAdmin->value
            && ! $request->user()->isSuperAdmin()
        ) {
            return response()->json(['success' => false, 'message' => 'Only Super Admin can assign Super Admin role.'], 403);
        }

        try {
            $user = $this->userService->update($user, $data);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->delete($user);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
