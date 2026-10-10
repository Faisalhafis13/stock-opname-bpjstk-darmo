<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Repositories\Contracts\RoleMenuRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RoleMenuController extends Controller
{
    public function __construct(
        protected RoleMenuRepositoryInterface $roleMenuRepository
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $roles = $this->roleMenuRepository->getRoles();
        $menus = $this->roleMenuRepository->getMenus();

        $requestedRoleId = $request->integer('role_id');

        $selectedRoleId = $roles->contains('id', $requestedRoleId)
            ? $requestedRoleId
            : $roles->first()?->id;

        $selectedMenuIds = $selectedRoleId
            ? $this->roleMenuRepository->getRoleMenuIds(
                $selectedRoleId
            )
            : [];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'role_id' => $selectedRoleId,
                'selected_menu_ids' => array_map(
                    'intval',
                    $selectedMenuIds
                ),
            ]);
        }

        return view('role-menu.index', compact(
            'roles',
            'menus',
            'selectedRoleId',
            'selectedMenuIds'
        ));
    }

    public function update(
        Request $request,
        int $roleId
    ): RedirectResponse|JsonResponse {
        abort_unless(Role::whereKey($roleId)->exists(), 404);

        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:menus,id',
            ],
        ], [
            'menu_ids.array' => 'Format pilihan menu tidak valid.',
            'menu_ids.*.integer' => 'Pilihan menu tidak valid.',
            'menu_ids.*.distinct' => 'Terdapat pilihan menu yang duplikat.',
            'menu_ids.*.exists' => 'Menu yang dipilih tidak ditemukan.',
        ]);

        $menuIds = $validated['menu_ids'] ?? [];

        $this->roleMenuRepository->syncMenus(
            $roleId,
            $menuIds
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses menu untuk role berhasil diperbarui.',
                'role_id' => $roleId,
                'selected_menu_ids' => array_map(
                    'intval',
                    $menuIds
                ),
            ]);
        }

        return redirect()
            ->route('role-menu.index', ['role_id' => $roleId])
            ->with(
                'success',
                'Hak akses menu untuk role berhasil diperbarui.'
            );
    }
}
