<?php

namespace App\Repositories\Contracts;

use App\Models\KategoriBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface KategoriBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator;

    public function findById(int $id): ?KategoriBarang;

    public function create(array $data): KategoriBarang;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}