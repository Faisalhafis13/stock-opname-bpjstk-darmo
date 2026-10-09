<?php

namespace App\Repositories\Contracts;

use App\Models\StokOpnameDetail;
use Illuminate\Support\Collection;

interface StokOpnameDetailRepositoryInterface
{
    public function getByPeriode(int $periodeId): Collection;

    public function findById(int $id): ?StokOpnameDetail;

    public function create(array $data): StokOpnameDetail;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}