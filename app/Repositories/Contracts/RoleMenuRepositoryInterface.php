<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Support\Collection;

interface RoleMenuRepositoryInterface
{
    public function getRoles(): Collection;

    public function getMenus(): Collection;

    public function getRoleMenuIds(int $roleId): array;

    public function syncMenus(int $roleId, array $menuIds): void;
}