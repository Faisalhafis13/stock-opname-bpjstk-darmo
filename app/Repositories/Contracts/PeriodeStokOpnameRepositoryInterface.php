<?php

namespace App\Repositories\Contracts;

use App\Models\PeriodeStokOpname;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PeriodeStokOpnameRepositoryInterface
{
    public function getAll(
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator;

    public function findById(
        int $id
    ): ?PeriodeStokOpname;

    public function create(
        array $data
    ): PeriodeStokOpname;

    public function update(
        int $id,
        array $data
    ): bool;

    public function delete(
        int $id
    ): bool;
}