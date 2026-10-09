<?php

namespace App\Repositories\Contracts;

use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator;

    public function findById(int $id): ?Barang;

    public function create(array $data): Barang;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}