<?php

namespace App\Repositories\Contracts;

use App\Models\PenambahanBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PenambahanBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator;

    public function findById(int $id): ?PenambahanBarang;

    public function create(array $data): PenambahanBarang;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}