<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Models\Role;
use App\Repositories\Contracts\RoleMenuRepositoryInterface;
use Illuminate\Support\Collection;

class RoleMenuRepository implements RoleMenuRepositoryInterface
{
    public function getRoles(): Collection
    {
        return Role::orderBy('name')->get();
    }

    public function getMenus(): Collection
    {
        return Menu::query()
            ->where('is_active', true)
            ->with([
                'children' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order');
                }
            ])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function getRoleMenuIds(int $roleId): array
    {
        return Role::findOrFail($roleId)
            ->menus()
            ->pluck('menus.id')
            ->toArray();
    }

    public function syncMenus(int $roleId, array $menuIds): void
    {
        $role = Role::findOrFail($roleId);

        $role->menus()->sync($menuIds);
    }
}