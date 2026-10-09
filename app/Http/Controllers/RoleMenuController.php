<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Repositories\Contracts\RoleMenuRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleMenuController extends Controller
{
    public function __construct(
        protected RoleMenuRepositoryInterface $roleMenuRepository
    ) {
    }

    public function index(Request $request): View
    {
        $roles = $this->roleMenuRepository->getRoles();
        $menus = $this->roleMenuRepository->getMenus();

        $requestedRoleId = $request->integer('role_id');

        $selectedRoleId = $roles->contains(
            'id',
            $requestedRoleId
        )
            ? $requestedRoleId
            : ($roles->first()?->id);

        $selectedMenuIds = $selectedRoleId
            ? $this->roleMenuRepository->getRoleMenuIds(
                $selectedRoleId
            )
            : [];

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
    ): RedirectResponse {
        $request->validate([
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

        abort_unless(Role::whereKey($roleId)->exists(), 404);

        $menuIds = $request->input('menu_ids', []);

        $this->roleMenuRepository->syncMenus(
            $roleId,
            $menuIds
        );

        return redirect()
            ->route('role-menu.index', [
                'role_id' => $roleId,
            ])
            ->with(
                'success',
                'Hak akses menu untuk role berhasil diperbarui.'
            );
    }
}