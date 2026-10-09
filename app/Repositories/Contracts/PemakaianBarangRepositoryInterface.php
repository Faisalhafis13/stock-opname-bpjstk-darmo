<?php

namespace App\Repositories\Contracts;

use App\Models\PemakaianBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PemakaianBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator;

    public function findById(int $id): ?PemakaianBarang;

    public function create(array $data): PemakaianBarang;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}